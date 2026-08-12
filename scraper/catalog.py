from __future__ import annotations

import hashlib
import re
import unicodedata
from dataclasses import dataclass, field

from .models import Product
from .normalize import clean_text, package_signature


@dataclass
class StrictCluster:
    cluster_id: str
    canonical: Product
    products: list[Product] = field(default_factory=list)

    def to_dict(self) -> dict:
        return {
            "cluster_id": self.cluster_id,
            "canonical_name": self.canonical.name,
            "canonical_source": self.canonical.source,
            "canonical_source_product_id": self.canonical.source_product_id,
            "min_price": self.canonical.price,
            "max_price": max(float(p.price or 0) for p in self.products),
            "candidate_sources": [
                {
                    "source": product.source,
                    "source_product_id": product.source_product_id,
                    "source_sku": product.source_sku,
                    "price": product.price,
                    "product_url": product.product_url,
                }
                for product in self.products
            ],
        }


def strict_clusters(products: list[Product]) -> list[StrictCluster]:
    groups: dict[str, list[Product]] = {}
    for product in products:
        groups.setdefault(_identity_key(product), []).append(product)

    clusters: list[StrictCluster] = []
    for key, candidates in sorted(groups.items()):
        candidates.sort(key=lambda item: (float(item.price or float("inf")), item.private_key))
        canonical = candidates[0]
        digest = hashlib.sha1("|".join(sorted(item.private_key for item in candidates)).encode()).hexdigest()[:14]
        clusters.append(StrictCluster(f"P{digest.upper()}", canonical, candidates))
    return clusters


def canonical_category_path(product: Product) -> list[str]:
    text = " ".join([product.name, *product.category_path]).lower()
    rules = [
        ("Mobiles & Tablets", r"mobile|smartphone|tablet|smart watch|wearable"),
        ("Computers & Office", r"laptop|computer|desktop|monitor|printer|scanner|router|keyboard|mouse"),
        ("TVs & Audio", r"\btv\b|television|projector|speaker|headphone|earbud|audio|soundbar"),
        ("Large Appliances", r"air conditioner|refrigerator|freezer|washing machine|dishwasher|cooker|oven"),
        ("Kitchen Appliances", r"kettle|blender|mixer|microwave|air fryer|coffee maker|toaster|food processor"),
        ("Gaming & Electronics", r"gaming|console|playstation|xbox|camera|electronics|accessor"),
    ]
    if product.source != "btech":
        rules.extend([
            ("Beverages", r"water|juice|drink|beverage|coffee|tea|soda|cola"),
            ("Pantry & Cooking", r"rice|pasta|oil|sauce|spice|flour|sugar|salt|canned|pantry|cooking"),
            ("Snacks & Confectionery", r"snack|chocolate|candy|biscuit|chips|sweet|confection"),
            ("Breakfast & Bakery", r"breakfast|cereal|oat|bakery|bread|jam|honey"),
            ("Household & Cleaning", r"clean|detergent|laundry|tissue|paper|household|dishwash"),
            ("Personal Care", r"shampoo|soap|deodorant|tooth|skin|beauty|personal care"),
            ("Baby & Family", r"baby|diaper|feminine|family"),
            ("Home & Kitchen", r"cookware|tableware|kitchen|home"),
        ])
    else:
        rules.extend([
            ("Personal Care", r"shaver|hair dryer|straightener|trimmer|personal care"),
            ("Home & Kitchen", r"home|furniture|tool|non-electronics"),
        ])
    for category, pattern in rules:
        if re.search(pattern, text):
            leaf = clean_text(product.category_path[-1]) if product.category_path else category
            return [category] if not leaf or leaf.lower() == category.lower() else [category, leaf]
    return ["Other"]


def _identity_key(product: Product) -> str:
    name = unicodedata.normalize("NFKC", product.name).lower()
    name = re.sub(r"[^\w]+", " ", name)
    name = re.sub(r"\s+", " ", name).strip()
    package = package_signature(product.name) or ""
    brand = clean_text((product.raw or {}).get("brand")).lower()
    return f"{brand}|{name}|{package}"
