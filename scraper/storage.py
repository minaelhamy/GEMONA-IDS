from __future__ import annotations

import csv
import json
import shutil
from pathlib import Path
from typing import Iterable

from .models import Product


def write_products(products: Iterable[Product], run_dir: Path) -> list[Product]:
    run_dir.mkdir(parents=True, exist_ok=True)
    materialized = list(products)
    jsonl_path = run_dir / "products.jsonl"
    csv_path = run_dir / "products.csv"

    with jsonl_path.open("w", encoding="utf-8") as handle:
        for product in materialized:
            handle.write(json.dumps(product.to_dict(), ensure_ascii=False) + "\n")

    fieldnames = list(materialized[0].to_dict().keys()) if materialized else []
    with csv_path.open("w", encoding="utf-8", newline="") as handle:
        if fieldnames:
            writer = csv.DictWriter(handle, fieldnames=fieldnames)
            writer.writeheader()
            for product in materialized:
                row = product.to_dict()
                row["category_path"] = " > ".join(row["category_path"])
                row["raw"] = json.dumps(row["raw"], ensure_ascii=False)
                writer.writerow(row)

    latest = Path("data/latest")
    latest.mkdir(parents=True, exist_ok=True)
    shutil.copy2(jsonl_path, latest / "products.jsonl")
    shutil.copy2(csv_path, latest / "products.csv")
    return materialized


def read_products(path: Path) -> list[Product]:
    products: list[Product] = []
    with path.open("r", encoding="utf-8") as handle:
        for line in handle:
            if not line.strip():
                continue
            data = json.loads(line)
            products.append(Product(**data))
    return products
