from __future__ import annotations

import json
import sys
from collections.abc import Iterable
from typing import Any
from urllib.parse import unquote

from ..http import HttpClient
from ..models import Product
from ..normalize import clean_text
from .base import Source


BTECH_CATEGORIES = [
    ("air-conditioners", "Air Conditioners"),
    ("mobiles-tablets", "Mobiles, Tablets & Accessories"),
    ("small-home-appliances", "Small Home Appliances"),
    ("large-home-appliances", "Large Home Appliances"),
    ("tvs-projectors", "TVs & Projectors"),
    ("laptop-pc", "Laptops, PCs & Accessories"),
    ("wearables", "Wearables"),
    ("personal-care", "Personal Care Appliances"),
    ("gaming-area", "Gaming"),
    ("electronics", "Electronics"),
    ("audio-recording", "Audio & Recording"),
    ("non-electronics", "Home & Non-Electronics"),
]


class BtechSource(Source):
    name = "btech"
    base_url = "https://btech.com"
    discovery_url = "https://retail-online-prod.btech.com/api/v1/green/discovery/api/v1"
    media_url = "https://media.btech.com/catalogs/"
    page_size = 100

    def __init__(self, client: HttpClient | None = None) -> None:
        super().__init__(client=client or HttpClient(delay_seconds=0.35))
        self._jwt: str | None = None
        self._detail_cache: dict[str, dict[str, Any]] = {}

    def scrape(self, *, query: str | None = None, limit: int | None = None) -> Iterable[Product]:
        query = clean_text(query)
        yield from self._crawl_filter(search_query=query or None, limit=limit)

    def crawl(
        self,
        *,
        limit: int | None = None,
        limit_categories: int | None = None,
    ) -> Iterable[Product]:
        categories = BTECH_CATEGORIES[: limit_categories or None]
        seen: set[str] = set()
        emitted = 0

        for slug, label in categories:
            try:
                for product in self._crawl_filter(category_slug=slug, category_label=label, limit=None):
                    if product.private_key in seen:
                        continue
                    seen.add(product.private_key)
                    yield product
                    emitted += 1
                    if limit and emitted >= limit:
                        return
            except RuntimeError as exc:
                print(f"btech: skipped category {slug}: {exc}", file=sys.stderr, flush=True)

    def _crawl_filter(
        self,
        *,
        category_slug: str | None = None,
        category_label: str | None = None,
        search_query: str | None = None,
        limit: int | None,
    ) -> Iterable[Product]:
        page = 1
        emitted = 0
        total_pages = 1

        while page <= total_pages:
            payload: dict[str, Any] = {
                "page": page,
                "page_size": min(self.page_size, limit or self.page_size),
                "filters": {"in_stock": True},
            }
            if page == 1:
                payload["include_filter_tree"] = True
            if category_slug:
                payload["filters"]["categories"] = category_slug
            if search_query:
                payload["search_query"] = search_query

            data = self._post("/products/search", payload)
            items = data.get("items") or []
            total_pages = int(data.get("total_pages") or 0)
            if not items:
                return

            for item in items:
                product = self._product_from_item(item, fallback_category=category_label)
                if product is None:
                    continue
                yield product
                emitted += 1
                if limit and emitted >= limit:
                    return
            page += 1

    def _product_from_item(self, item: dict[str, Any], *, fallback_category: str | None) -> Product | None:
        variant_id = clean_text(item.get("variant_id"))
        name = clean_text(item.get("name"))
        thumbnail = clean_text(item.get("thumbnail_url"))
        price = (item.get("price") or {}).get("final_price")
        if not variant_id or not name or not thumbnail or price is None or not item.get("is_in_stock", False):
            return None

        detail_slug = clean_text(item.get("slug")) or variant_id
        try:
            detail = self._product_detail(detail_slug, cache_key=clean_text(item.get("product_id")) or detail_slug)
        except RuntimeError as exc:
            print(f"btech: skipped product {variant_id}: {exc}", file=sys.stderr, flush=True)
            return None
        variant = (detail.get("variants") or {}).get(variant_id) or {}
        variant_name = clean_text(variant.get("name")) or name
        main_image = clean_text(variant.get("main_image")) or thumbnail
        variant_price = (variant.get("price") or {}).get("final_price")
        categories = detail.get("categories") or item.get("categories") or {}
        category_path = [
            clean_text((categories.get(level) or {}).get("name"))
            for level in ("l1", "l2", "l3")
        ]
        category_path = [part for part in category_path if part]
        if not category_path and fallback_category:
            category_path = [fallback_category]

        attributes = variant.get("attributes") or {}
        specifications = detail.get("specifications") or []
        description_parts = [clean_text(detail.get("description"))]
        description_parts.extend(
            f"{clean_text(spec.get('key'))}: {clean_text(spec.get('value'))}"
            for spec in specifications
            if clean_text(spec.get("key")) and clean_text(spec.get("value"))
        )

        slug = clean_text(variant.get("slug")) or clean_text(item.get("slug")) or variant_id
        offer_id = clean_text(variant.get("offer_id")) or clean_text(item.get("offer_id"))
        product_url = f"{self.base_url}/en/p/{slug}"
        if offer_id:
            product_url += f"?offering_id={offer_id}"

        return Product(
            source=self.name,
            source_product_id=variant_id,
            source_sku=clean_text(item.get("sku")) or clean_text(variant.get("platform_fulfillment_id")) or None,
            name=variant_name,
            price=variant_price if variant_price is not None else price,
            currency="EGP",
            image_url=self._absolute_media_url(main_image),
            description=clean_text(detail.get("description")) or None,
            detail="\n".join(part for part in description_parts[1:] if part) or None,
            product_url=product_url,
            category_path=category_path,
            raw={
                "in_stock": bool(variant.get("in_stock", item.get("is_in_stock", True))),
                "brand": clean_text(detail.get("brand")) or clean_text(item.get("brand")) or None,
                "product_id": clean_text(item.get("product_id")) or None,
                "offer_id": offer_id or None,
                "attributes": attributes,
                "specifications": specifications,
                "media_gallery": [self._absolute_media_url(path) for path in variant.get("media_gallery") or []],
                "is_sold_by_btech": bool(variant.get("is_sold_by_btech", False)),
                "is_fulfilled_by_btech": bool(variant.get("is_fulfilled_by_btech", False)),
            },
        )

    def _product_detail(self, slug: str, *, cache_key: str) -> dict[str, Any]:
        if cache_key not in self._detail_cache:
            self._detail_cache[cache_key] = self._get(f"/products/{slug}")
        return self._detail_cache[cache_key]

    def _get(self, path: str) -> dict[str, Any]:
        headers = self._auth_headers()
        result = self.client.get(
            f"{self.discovery_url}{path}",
            headers={"Accept": "application/json", **headers},
        )
        if result.status_code == 401:
            self._jwt = None
            result = self.client.get(
                f"{self.discovery_url}{path}",
                headers={"Accept": "application/json", **self._auth_headers()},
            )
        return self._decode_response(result.status_code, result.text, path)

    def _post(self, path: str, payload: dict[str, Any]) -> dict[str, Any]:
        result = self.client.post_json(
            f"{self.discovery_url}{path}",
            payload,
            headers=self._auth_headers(),
        )
        if result.status_code == 401:
            self._jwt = None
            result = self.client.post_json(
                f"{self.discovery_url}{path}",
                payload,
                headers=self._auth_headers(),
            )
        return self._decode_response(result.status_code, result.text, path)

    def _auth_headers(self) -> dict[str, str]:
        if self._jwt is None:
            result = self.client.get(f"{self.base_url}/en")
            if result.status_code >= 400:
                raise RuntimeError(f"B.TECH guest session failed with HTTP {result.status_code}")
            encoded = self.client.session.cookies.get("btech-auth-session")
            if not encoded:
                raise RuntimeError("B.TECH guest session cookie was not returned")
            try:
                session = json.loads(unquote(encoded))
                self._jwt = clean_text(session.get("JWT"))
            except (TypeError, ValueError, json.JSONDecodeError) as exc:
                raise RuntimeError("B.TECH guest session cookie was invalid") from exc
            if not self._jwt:
                raise RuntimeError("B.TECH guest JWT was not returned")

        return {
            "Authorization": f"Bearer {self._jwt}",
            "Accept-Language": "en",
        }

    @staticmethod
    def _decode_response(status_code: int, text: str, path: str) -> dict[str, Any]:
        if status_code >= 400:
            raise RuntimeError(f"B.TECH {path} failed with HTTP {status_code}")
        try:
            payload = json.loads(text)
        except json.JSONDecodeError as exc:
            raise RuntimeError(f"B.TECH {path} returned invalid JSON") from exc
        if not isinstance(payload, dict):
            raise RuntimeError(f"B.TECH {path} returned an unexpected response")
        return payload

    def _absolute_media_url(self, path: str) -> str:
        path = clean_text(path)
        if path.startswith("http://") or path.startswith("https://"):
            return path
        return f"{self.media_url}{path.lstrip('/')}"
