from __future__ import annotations

import json
from collections.abc import Iterable
from typing import Any
from urllib.parse import quote_plus

from ..models import Product
from ..normalize import clean_text, parse_price, should_skip_cold_chain
from ..settings import DEFAULT_LOCATION
from .base import Source


class MahmoudElFarSource(Source):
    name = "mahmoud_elfar"
    base_url = "https://mahmoudelfar.com/"
    api_url = "https://api.mahmoudelfar.com/api/"
    fallback_home_category_ids = [
        344, 351, 61, 374, 154, 78, 57, 97, 144, 66, 82, 121,
        138, 132, 356, 160, 159, 262, 183, 193, 203,
    ]

    def scrape(self, *, query: str | None = None, limit: int | None = None) -> Iterable[Product]:
        query = clean_text(query) or "water"
        channel_id = self._select_location()
        product_ids = self._search_ids(query, channel_id=channel_id)
        if limit:
            product_ids = product_ids[:limit]
        yield from self._products_list(product_ids, channel_id=channel_id, limit=limit)

    def crawl(
        self,
        *,
        limit: int | None = None,
        limit_categories: int | None = None,
    ) -> Iterable[Product]:
        channel_id = self._select_location()
        categories = self.discover_categories(channel_id=channel_id)
        if limit_categories:
            categories = categories[:limit_categories]
        seen: set[str] = set()
        emitted = 0
        for category in categories:
            for product in self._crawl_category(category, channel_id=channel_id):
                if product.private_key in seen:
                    continue
                seen.add(product.private_key)
                yield product
                emitted += 1
                if limit and emitted >= limit:
                    return

    def discover_categories(self, *, channel_id: int | None = None) -> list[dict[str, Any]]:
        channel_id = channel_id or self._select_location()
        ids = self._home_category_ids(channel_id=channel_id)
        data = self._get(f"home/categories?categories_ids={','.join(str(item) for item in ids)}", channel_id=channel_id)
        leaves: list[dict[str, Any]] = []
        for node in data.get("data") or []:
            self._collect_category_leaves(node, [], leaves)
        unique: dict[str, dict[str, Any]] = {}
        for category in leaves:
            unique[str(category["id"])] = category
        return list(unique.values())

    def _select_location(self) -> int:
        payload = {
            "lat": DEFAULT_LOCATION["lat"],
            "lng": DEFAULT_LOCATION["lng"],
            "is_pin": "1",
            "district_id": "0",
            "address_id": "",
            "address": DEFAULT_LOCATION["label"],
        }
        data = self._post("geofencing/select/branch", payload, channel_id=2)
        return int((data.get("data") or {}).get("channel_id") or 2)

    def _search_ids(self, query: str, *, channel_id: int) -> list[str]:
        data = self._get(f"products/search?query={quote_plus(query)}", channel_id=channel_id)
        ids: list[str] = []
        for item in data.get("data") or []:
            product_id = item.get("id")
            if product_id is not None:
                ids.append(str(product_id))
        return ids

    def _home_category_ids(self, *, channel_id: int) -> list[int]:
        try:
            data = self._get("home/sections", channel_id=channel_id)
        except Exception:
            return self.fallback_home_category_ids
        for section in data.get("data") or []:
            params = section.get("params") or {}
            raw_ids = params.get("categories_ids")
            if raw_ids:
                return [int(item) for item in str(raw_ids).split(",") if item.strip().isdigit()]
        return self.fallback_home_category_ids

    def _crawl_category(self, category: dict[str, Any], *, channel_id: int) -> Iterable[Product]:
        page = 1
        last_page = None
        while last_page is None or page <= last_page:
            data = self._get(
                f"products?category_id={category['id']}&page={page}&limit=48",
                channel_id=channel_id,
            )
            meta = data.get("meta") or {}
            last_page = int(meta.get("last_page") or page)
            items = data.get("data") or []
            if not items:
                return
            for item in items:
                product = self._product_from_item(item, category_path=category.get("_category_path") or [])
                if product:
                    yield product
            page += 1

    def _products_list(
        self,
        product_ids: list[str],
        *,
        channel_id: int,
        limit: int | None,
    ) -> Iterable[Product]:
        emitted = 0
        seen: set[str] = set()
        for start in range(0, len(product_ids), 48):
            batch = product_ids[start : start + 48]
            if not batch:
                return
            data = self._post(
                "products/list",
                {"product_ids": batch, "page": 1, "limit": "48"},
                channel_id=channel_id,
            )
            for item in data.get("data") or []:
                product = self._product_from_item(item)
                if product is None or product.private_key in seen:
                    continue
                seen.add(product.private_key)
                yield product
                emitted += 1
                if limit and emitted >= limit:
                    return

    def _get(self, path: str, *, channel_id: int) -> dict[str, Any]:
        url = self._url(path, channel_id=channel_id)
        result = self.client.get(
            url,
            headers={
                "Accept": "application/json",
                "Origin": self.base_url.rstrip("/"),
                "Referer": self.base_url,
            },
        )
        return self._json_result(result.text, result.status_code, path)

    def _post(self, path: str, payload: dict[str, Any], *, channel_id: int) -> dict[str, Any]:
        result = self.client.post_json(
            self._url(path, channel_id=channel_id),
            payload,
            headers={
                "Origin": self.base_url.rstrip("/"),
                "Referer": self.base_url,
            },
        )
        return self._json_result(result.text, result.status_code, path)

    def _url(self, path: str, *, channel_id: int) -> str:
        separator = "&" if "?" in path else "?"
        return f"{self.api_url}{path}{separator}locale=en&token=true&channel_id={channel_id}"

    def _json_result(self, text: str, status_code: int, path: str) -> dict[str, Any]:
        if status_code >= 400:
            raise RuntimeError(f"Mahmoud El Far API {path} failed with HTTP {status_code}")
        payload = json.loads(text)
        code = (payload.get("status") or {}).get("code")
        if code is not None and not (200 <= int(code) < 300 or int(code) == 451):
            raise RuntimeError(f"Mahmoud El Far API {path} failed: {payload.get('status')}")
        return payload

    def _product_from_item(
        self,
        item: dict[str, Any],
        *,
        category_path: list[str] | None = None,
    ) -> Product | None:
        name = clean_text(item.get("name"))
        detail_parts = [clean_text(item.get("weight")), clean_text(item.get("type"))]
        category_path = category_path or []
        if not name or should_skip_cold_chain(name, " ".join(detail_parts), " ".join(category_path)):
            return None
        images = item.get("base_image") or {}
        image_url = (
            images.get("original_image_url")
            or images.get("large_image_url")
            or images.get("medium_image_url")
            or images.get("small_image_url")
        )
        product_id = str(item.get("id")) if item.get("id") is not None else None
        product_url = item.get("url_key")
        return Product(
            source=self.name,
            source_product_id=product_id,
            source_sku=self._barcode_from_slug(clean_text(item.get("slug"))),
            name=name,
            price=parse_price(str(item.get("final_pr") or item.get("final_price") or "")),
            currency=item.get("currency_code") or "EGP",
            image_url=image_url,
            description=None,
            detail="; ".join(part for part in detail_parts if part) or None,
            product_url=product_url,
            category_path=category_path,
            raw={
                "in_stock": item.get("in_stock"),
                "available_quantity": item.get("available_quantity"),
                "discount": item.get("discount"),
            },
        )

    def _barcode_from_slug(self, slug: str) -> str | None:
        tail = slug.rsplit("-", 1)[-1]
        return tail if tail.isdigit() and 8 <= len(tail) <= 14 else None

    def _collect_category_leaves(
        self,
        node: dict[str, Any],
        parents: list[str],
        leaves: list[dict[str, Any]],
    ) -> None:
        if str(node.get("status", "1")) != "1":
            return
        name = clean_text(node.get("name"))
        path = [*parents, name] if name else parents
        if should_skip_cold_chain(" ".join(path)):
            return
        children = node.get("children") or []
        active_children = [child for child in children if str(child.get("status", "1")) == "1"]
        if not active_children and node.get("id") is not None:
            leaves.append({**node, "_category_path": path})
            return
        for child in active_children:
            self._collect_category_leaves(child, path, leaves)
