from __future__ import annotations

import unittest
import tempfile
import json
from pathlib import Path

from scraper.catalog import canonical_category_path, strict_clusters
from scraper.models import Product
from scraper.cli import _deduplicate_checkpoint


class CatalogTests(unittest.TestCase):
    def product(self, source: str, source_id: str, name: str, price: float, category: str = "") -> Product:
        return Product(
            source=source,
            source_product_id=source_id,
            source_sku=None,
            name=name,
            price=price,
            image_url=f"https://example.test/{source_id}.jpg",
            category_path=[category] if category else [],
            local_image_path=f"/stage/{source_id}.jpg",
            image_sha256=source_id.rjust(64, "0"),
        )

    def test_strict_clusters_keep_different_sizes_separate(self) -> None:
        products = [
            self.product("seoudi", "1", "Pepsi 330 ml", 10),
            self.product("hyperone", "2", "Pepsi 1.5 L", 30),
        ]
        self.assertEqual(2, len(strict_clusters(products)))

    def test_cluster_canonical_fields_come_from_same_cheapest_row(self) -> None:
        products = [
            self.product("seoudi", "1", "Pepsi 1.5 L", 35),
            self.product("hyperone", "2", "Pepsi 1.5 L", 30),
        ]
        cluster = strict_clusters(products)[0]
        self.assertEqual("hyperone", cluster.canonical.source)
        self.assertEqual("2", cluster.canonical.source_product_id)
        self.assertEqual("Pepsi 1.5 L", cluster.canonical.name)

    def test_hyperone_appliance_gets_appliance_category(self) -> None:
        product = self.product(
            "hyperone",
            "3",
            "Sharp Front Load Washing Machine 8 kg",
            20_000,
            "Appliances",
        )
        self.assertEqual("Large Appliances", canonical_category_path(product)[0])

    def test_grocery_description_recovers_bad_supplier_category(self) -> None:
        product = self.product("seoudi", "4", "Seoudi Tomato Paste - 360 g", 32.5, "Lamb Chops")
        product.detail = "Tomato sauce for pasta and stews"
        self.assertEqual(["Pantry & Cooking"], canonical_category_path(product))

    def test_household_disposable_gets_home_category(self) -> None:
        product = self.product("hyperone", "5", "Plastic White Plates - 25 Pieces", 50)
        self.assertEqual(["Home & Kitchen"], canonical_category_path(product))

    def test_checkpoint_deduplication_keeps_one_source_row(self) -> None:
        with tempfile.TemporaryDirectory() as directory:
            path = Path(directory) / "checkpoint.jsonl"
            product = self.product("seoudi", "1", "Tahini 450 g", 100)
            path.write_text(
                json.dumps(product.to_dict()) + "\n" + json.dumps(product.to_dict()) + "\n",
                encoding="utf-8",
            )
            rows = _deduplicate_checkpoint(path, require_local_image=False)
            self.assertEqual(1, len(rows))
            self.assertEqual(1, len(path.read_text(encoding="utf-8").splitlines()))


if __name__ == "__main__":
    unittest.main()
