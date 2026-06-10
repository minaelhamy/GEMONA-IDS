from __future__ import annotations

import argparse
import json
import sys
from datetime import datetime, timezone
from pathlib import Path

from .dedupe import cluster_products
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
