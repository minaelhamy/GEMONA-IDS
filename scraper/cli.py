from __future__ import annotations

import argparse
import hashlib
import json
import sys
import threading
from concurrent.futures import ThreadPoolExecutor, as_completed
from datetime import datetime, timezone
from pathlib import Path
from collections import defaultdict

from .dedupe import cluster_products
from .catalog import canonical_category_path, strict_clusters
from .http import HttpClient
from .images import ImageValidationError, stage_product_image
from .sources import SOURCES
from .storage import read_products, write_products


def scrape(args: argparse.Namespace) -> None:
    source_cls = SOURCES[args.source]
    source = source_cls()
    timestamp = datetime.now(timezone.utc).strftime("%Y%m%dT%H%M%SZ")
    run_dir = Path("data/runs") / timestamp
    products = write_products(source.scrape(query=args.query, limit=args.limit), run_dir)
    print(f"Wrote {len(products)} products to {run_dir}")


def scrape_many(args: argparse.Namespace) -> None:
    timestamp = datetime.now(timezone.utc).strftime("%Y%m%dT%H%M%SZ")
    run_dir = Path("data/runs") / timestamp
    products = []
    for source_name in args.sources:
        source_cls = SOURCES[source_name]
        source = source_cls()
        source_products = list(source.scrape(query=args.query, limit=args.limit_per_source))
        products.extend(source_products)
        print(f"{source_name}: {len(source_products)} products")
    written = write_products(products, run_dir)
    print(f"Wrote {len(written)} products to {run_dir}")


def crawl(args: argparse.Namespace) -> None:
    source_cls = SOURCES[args.source]
    source = source_cls()
    timestamp = datetime.now(timezone.utc).strftime("%Y%m%dT%H%M%SZ")
    run_dir = Path("data/runs") / timestamp
    products = []
    try:
        for product in source.crawl(limit=args.limit, limit_categories=args.limit_categories):
            products.append(product)
            if len(products) % args.progress_every == 0:
                print(f"{args.source}: {len(products)} products...", flush=True)
    except Exception as exc:
        if products:
            write_products(products, run_dir)
            print(f"Wrote partial {len(products)} products to {run_dir}", file=sys.stderr, flush=True)
        raise exc
    products = write_products(products, run_dir)
    print(f"Wrote {len(products)} products to {run_dir}")


def crawl_many(args: argparse.Namespace) -> None:
    timestamp = datetime.now(timezone.utc).strftime("%Y%m%dT%H%M%SZ")
    run_dir = Path("data/runs") / timestamp
    products = []
    for source_name in args.sources:
        source_cls = SOURCES[source_name]
        source = source_cls()
        print(f"{source_name}: starting crawl", flush=True)
        source_products = []
        try:
            for product in source.crawl(
                limit=args.limit_per_source,
                limit_categories=args.limit_categories_per_source,
            ):
                source_products.append(product)
                if len(source_products) % args.progress_every == 0:
                    print(f"{source_name}: {len(source_products)} products...", flush=True)
        except Exception as exc:
            products.extend(source_products)
            if products:
                write_products(products, run_dir)
                print(f"Wrote partial {len(products)} products to {run_dir}", file=sys.stderr, flush=True)
            raise exc
        products.extend(source_products)
        print(f"{source_name}: {len(source_products)} products", flush=True)
    written = write_products(products, run_dir)
    print(f"Wrote {len(written)} products to {run_dir}")


def dedupe(args: argparse.Namespace) -> None:
    products = read_products(Path(args.input))
    clusters = cluster_products(products, fuzzy_threshold=args.threshold)
    output = Path(args.output) if args.output else Path(args.input).with_name("clusters.json")
    with output.open("w", encoding="utf-8") as handle:
        json.dump([cluster.to_dict() for cluster in clusters], handle, ensure_ascii=False, indent=2)
    print(f"Wrote {len(clusters)} clusters to {output}")


def merge(args: argparse.Namespace) -> None:
    timestamp = datetime.now(timezone.utc).strftime("%Y%m%dT%H%M%SZ")
    run_dir = Path(args.output_dir) if args.output_dir else Path("data/runs") / timestamp
    products = []
    for input_path in args.inputs:
        source_products = read_products(Path(input_path))
        products.extend(source_products)
        print(f"{input_path}: {len(source_products)} products", flush=True)
    written = write_products(products, run_dir)
    print(f"Wrote {len(written)} products to {run_dir}")


def stage_catalog(args: argparse.Namespace) -> None:
    run_dir = Path(args.output_dir)
    image_dir = run_dir / "images"
    run_dir.mkdir(parents=True, exist_ok=True)
    staged = []
    rejected = []

    for source_name in args.sources:
        checkpoint_dir = run_dir / "checkpoints"
        checkpoint_dir.mkdir(parents=True, exist_ok=True)
        discovered_path = checkpoint_dir / f"{source_name}-discovered.jsonl"
        discovery_complete = checkpoint_dir / f"{source_name}-discovery.complete"
        staged_path = checkpoint_dir / f"{source_name}-staged.jsonl"
        staging_complete = checkpoint_dir / f"{source_name}-staging.complete"

        if discovery_complete.is_file() and discovered_path.is_file():
            candidates = read_products(discovered_path)
            print(f"{source_name}: resumed {len(candidates)} discovered products.", flush=True)
        else:
            source = SOURCES[source_name]()
            candidates = read_products(discovered_path) if discovered_path.is_file() else []
            seen: set[str] = {product.private_key for product in candidates}
            if candidates:
                print(f"{source_name}: continuing partial discovery after {len(candidates)} products.", flush=True)
            with discovered_path.open("a", encoding="utf-8") as discovered_file:
                for product in source.crawl(limit=args.limit_per_source, limit_categories=args.limit_categories_per_source):
                    if product.private_key in seen or product.price is None or float(product.price) <= 0:
                        continue
                    seen.add(product.private_key)
                    product.category_path = canonical_category_path(product)
                    candidates.append(product)
                    discovered_file.write(json.dumps(product.to_dict(), ensure_ascii=False) + "\n")
                    discovered_file.flush()
                    if len(candidates) % 500 == 0:
                        print(f"{source_name}: discovered {len(candidates)} source products...", flush=True)
            discovery_complete.touch()

        source_staged = read_products(staged_path) if staged_path.is_file() else []
        source_staged = [product for product in source_staged if product.local_image_path and Path(product.local_image_path).is_file()]
        staged_keys = {product.private_key for product in source_staged}
        staged.extend(source_staged)
        source_count = len(source_staged)
        if staging_complete.is_file():
            print(f"{source_name}: resumed completed staging with {source_count} products.", flush=True)
            continue
        pending = [product for product in candidates if product.private_key not in staged_keys]
        print(f"{source_name}: validating {len(pending)} remaining images with {args.image_workers} workers.", flush=True)

        thread_state = threading.local()

        def stage_one(product):
            if not hasattr(thread_state, "client"):
                thread_state.client = HttpClient(delay_seconds=0.05)
            return stage_product_image(product, image_dir, thread_state.client)

        with ThreadPoolExecutor(max_workers=args.image_workers) as executor:
            with staged_path.open("a", encoding="utf-8") as staged_file:
                for offset in range(0, len(pending), args.image_batch_size):
                    batch = pending[offset : offset + args.image_batch_size]
                    futures = {executor.submit(stage_one, product): product for product in batch}
                    for future in as_completed(futures):
                        product = futures[future]
                        try:
                            staged_product = future.result()
                        except (ImageValidationError, OSError, RuntimeError) as exc:
                            rejected.append({"private_key": product.private_key, "name": product.name, "reason": str(exc)})
                            continue
                        staged.append(staged_product)
                        staged_file.write(json.dumps(staged_product.to_dict(), ensure_ascii=False) + "\n")
                        staged_file.flush()
                        source_count += 1
                        if source_count % args.progress_every == 0:
                            print(f"{source_name}: {source_count} products with validated local images...", flush=True)
        staging_complete.touch()
        print(f"{source_name}: staged {source_count}; rejected {sum(1 for r in rejected if r['private_key'].startswith(source_name + ':'))}")

    hash_groups: dict[str, list] = defaultdict(list)
    for product in staged:
        hash_groups[product.image_sha256 or ""].append(product)
    reused_placeholders = {
        digest
        for digest, products in hash_groups.items()
        if digest and len({product.name.casefold() for product in products}) >= 3
    }
    if reused_placeholders:
        retained = []
        for product in staged:
            if product.image_sha256 in reused_placeholders:
                if product.local_image_path:
                    Path(product.local_image_path).unlink(missing_ok=True)
                rejected.append({
                    "private_key": product.private_key,
                    "name": product.name,
                    "reason": "same image reused by at least three differently named products",
                })
            else:
                retained.append(product)
        staged = retained
        print(f"Rejected {sum(len(hash_groups[digest]) for digest in reused_placeholders)} products using repeated placeholder images.")

    products_path = run_dir / "products.jsonl"
    with products_path.open("w", encoding="utf-8") as handle:
        for product in staged:
            handle.write(json.dumps(product.to_dict(), ensure_ascii=False) + "\n")
    clusters = strict_clusters(staged)
    clusters_path = run_dir / "clusters.json"
    clusters_path.write_text(json.dumps([cluster.to_dict() for cluster in clusters], ensure_ascii=False, indent=2), encoding="utf-8")
    (run_dir / "rejected.json").write_text(json.dumps(rejected, ensure_ascii=False, indent=2), encoding="utf-8")
    manifest = {
        "sources": args.sources,
        "product_count": len(staged),
        "cluster_count": len(clusters),
        "rejected_count": len(rejected),
        "margin_percent": 15,
        "products_sha256": hashlib.sha256(products_path.read_bytes()).hexdigest(),
        "clusters_sha256": hashlib.sha256(clusters_path.read_bytes()).hexdigest(),
    }
    (run_dir / "manifest.json").write_text(json.dumps(manifest, indent=2), encoding="utf-8")
    print(f"Staged {len(staged)} products in {len(clusters)} strict clusters at {run_dir}")


def import_carrefour_html(args: argparse.Namespace) -> None:
    source_cls = SOURCES["carrefour"]
    source = source_cls()
    timestamp = datetime.now(timezone.utc).strftime("%Y%m%dT%H%M%SZ")
    run_dir = Path(args.output_dir) if args.output_dir else Path("data/runs") / timestamp
    products = []
    category_path = args.category_path.split(">") if args.category_path else None
    for input_path in args.inputs:
        html = Path(input_path).read_text(encoding="utf-8")
        source_products = list(source.products_from_html(html, category_path=category_path))
        products.extend(source_products)
        print(f"{input_path}: {len(source_products)} products", flush=True)
    written = write_products(products, run_dir)
    print(f"Wrote {len(written)} products to {run_dir}")


def build_parser() -> argparse.ArgumentParser:
    parser = argparse.ArgumentParser(description="Egypt grocery product scraper")
    sub = parser.add_subparsers(required=True)

    scrape_parser = sub.add_parser("scrape", help="Scrape one source")
    scrape_parser.add_argument("--source", required=True, choices=sorted(SOURCES))
    scrape_parser.add_argument("--query", help="Search query or source-specific category slug")
    scrape_parser.add_argument("--limit", type=int, help="Maximum products to output")
    scrape_parser.set_defaults(func=scrape)

    scrape_many_parser = sub.add_parser("scrape-many", help="Scrape multiple sources into one dataset")
    scrape_many_parser.add_argument("--sources", nargs="+", required=True, choices=sorted(SOURCES))
    scrape_many_parser.add_argument("--query", help="Search query or shared category slug")
    scrape_many_parser.add_argument("--limit-per-source", type=int, help="Maximum products per source")
    scrape_many_parser.set_defaults(func=scrape_many)

    crawl_parser = sub.add_parser("crawl", help="Crawl one source's non-cold full catalog")
    crawl_parser.add_argument("--source", required=True, choices=sorted(SOURCES))
    crawl_parser.add_argument("--limit", type=int, help="Maximum products to output")
    crawl_parser.add_argument("--limit-categories", type=int, help="Maximum categories to crawl")
    crawl_parser.add_argument("--progress-every", type=int, default=250, help="Print progress every N products")
    crawl_parser.set_defaults(func=crawl)

    crawl_many_parser = sub.add_parser("crawl-many", help="Crawl multiple full catalogs into one dataset")
    crawl_many_parser.add_argument("--sources", nargs="+", required=True, choices=sorted(SOURCES))
    crawl_many_parser.add_argument("--limit-per-source", type=int, help="Maximum products per source")
    crawl_many_parser.add_argument("--limit-categories-per-source", type=int, help="Maximum categories per source")
    crawl_many_parser.add_argument("--progress-every", type=int, default=250, help="Print progress every N products")
    crawl_many_parser.set_defaults(func=crawl_many)

    dedupe_parser = sub.add_parser("dedupe", help="Cluster likely duplicate products")
    dedupe_parser.add_argument("input", help="Path to products.jsonl")
    dedupe_parser.add_argument("--output", help="Path to write clusters JSON")
    dedupe_parser.add_argument("--threshold", type=float, default=0.88)
    dedupe_parser.set_defaults(func=dedupe)

    merge_parser = sub.add_parser("merge", help="Merge existing JSONL product files into one dataset")
    merge_parser.add_argument("inputs", nargs="+", help="Product JSONL files to merge")
    merge_parser.add_argument("--output-dir", help="Directory for the merged products output")
    merge_parser.set_defaults(func=merge)

    stage_parser = sub.add_parser("stage-catalog", help="Crawl and retain only products with validated local images")
    stage_parser.add_argument("--sources", nargs="+", required=True, choices=sorted(SOURCES))
    stage_parser.add_argument("--output-dir", required=True)
    stage_parser.add_argument("--limit-per-source", type=int)
    stage_parser.add_argument("--limit-categories-per-source", type=int)
    stage_parser.add_argument("--progress-every", type=int, default=100)
    stage_parser.add_argument("--image-workers", type=int, default=8)
    stage_parser.add_argument("--image-batch-size", type=int, default=100)
    stage_parser.set_defaults(func=stage_catalog)

    html_parser = sub.add_parser("import-carrefour-html", help="Import rendered Carrefour category HTML snapshots")
    html_parser.add_argument("inputs", nargs="+", help="Rendered Carrefour category HTML files")
    html_parser.add_argument("--category-path", help="Optional category path, separated with >")
    html_parser.add_argument("--output-dir", help="Directory for imported products output")
    html_parser.set_defaults(func=import_carrefour_html)
    return parser


def main() -> None:
    parser = build_parser()
    args = parser.parse_args()
    args.func(args)


if __name__ == "__main__":
    main()
