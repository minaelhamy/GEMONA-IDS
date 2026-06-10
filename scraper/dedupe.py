from __future__ import annotations

from collections import defaultdict
from dataclasses import dataclass, field

from .models import Product
from .normalize import blocking_key, package_signature, similarity


@dataclass
class Cluster:
    cluster_id: str
    canonical_name: str
    products: list[Product] = field(default_factory=list)

    def to_dict(self) -> dict:
        prices = [p.price for p in self.products if p.price is not None]
        return {
            "cluster_id": self.cluster_id,
            "canonical_name": self.canonical_name,
            "package_signature": package_signature(self.canonical_name),
            "min_price": min(prices) if prices else None,
            "max_price": max(prices) if prices else None,
            "candidate_sources": [
                {
                    "source": p.source,
                    "source_product_id": p.source_product_id,
                    "source_sku": p.source_sku,
                    "price": p.price,
                    "product_url": p.product_url,
                }
                for p in self.products
            ],
        }


def cluster_products(products: list[Product], fuzzy_threshold: float = 0.88) -> list[Cluster]:
    buckets: dict[str, list[Product]] = defaultdict(list)
    for product in products:
        category_hint = product.category_path[-1] if product.category_path else None
        buckets[blocking_key(product.name, product.source_sku, category_hint)].append(product)

    clusters: list[Cluster] = []
    counter = 1
    for bucket in buckets.values():
        local: list[Cluster] = []
        for product in bucket:
            match = None
            for cluster in local:
                if similarity(product.name, cluster.canonical_name) >= fuzzy_threshold:
                    match = cluster
                    break
            if match:
                match.products.append(product)
            else:
                local.append(Cluster(cluster_id=f"C{counter:06d}", canonical_name=product.name, products=[product]))
                counter += 1
        clusters.extend(local)
    return clusters
