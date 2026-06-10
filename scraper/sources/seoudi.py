from __future__ import annotations

import json
import re
import sys
import time
from collections.abc import Iterable
from html import unescape
from typing import Any

from bs4 import BeautifulSoup

from ..models import Product
from ..normalize import clean_text, should_skip_cold_chain
from .base import Source


PRODUCT_FIELDS = """
id
sku
name
url_key
stock_status
small_image { url label }
categories { name url_path }
price_range {
  minimum_price {
    final_price { value currency }
    regular_price { value currency }
  }
}
short_description { html }
description { html }
"""


class SeoudiSource(Source):
    name = "seoudi"
    base_url = "https://seoudisupermarket.com/en/"
    graphql_url = "https://mcprod.seoudisupermarket.com/graphql"

    def scrape(self, *, query: str | None = None, limit: int | None = None) -> Iterable[Product]:
        query = clean_text(query) or "water"
        category = self._find_category(query.strip("/"), page_size=min(limit or 48, 48))
        if category:
            yield from self._scrape_category(query.strip("/"), category=category, limit=limit)
            return
        yield from self._scrape_search(query, limit=limit)

    def crawl(
        self,
        *,
        limit: int | None = None,
        limit_categories: int | None = None,
    ) -> Iterable[Product]:
        seen: set[str] = set()
        emitted = 0
        categories = self.discover_categories()
        if limit_categories:
            categories = categories[:limit_categories]
        for category in categories:
            slug = clean_text(category.get("url_path") or category.get("url_key"))
            if not slug:
                continue
            try:
                category_products = self._scrape_category(slug, category=category, limit=None)
                for product in category_products:
                    if product.private_key in seen:
                        continue
                    seen.add(product.private_key)
                    yield product
                    emitted += 1
                    if limit and emitted >= limit:
                        return
            except RuntimeError as exc:
                print(f"seoudi: skipped category {slug}: {exc}", file=sys.stderr, flush=True)
                continue

    def discover_categories(self) -> list[dict[str, Any]]:
        document = (
            "query CategoryTree { "
            "categoryList(filters: { ids: { in: [\"2\"] } }) { "
            "id name url_key url_path product_count children { "
            "id name url_key url_path product_count children { "
            "id name url_key url_path product_count children { "
            "id name url_key url_path product_count children { "
            "id name url_key url_path product_count "
            "} } } } } }"
        )
        roots = self._graphql(document, {}).get("categoryList", [])
        leaves: list[dict[str, Any]] = []
        for root in roots:
            self._collect_category_leaves(root, [], leaves)
        unique: dict[str, dict[str, Any]] = {}
        for category in leaves:
            key = str(category.get("id") or category.get("url_path") or category.get("url_key"))
            unique[key] = category
        return list(unique.values())

    def _scrape_search(self, query: str, *, limit: int | None) -> Iterable[Product]:
        document = (
            "query Products($search: String!, $pageSize: Int!, $currentPage: Int!) { "
            "products(search: $search, pageSize: $pageSize, currentPage: $currentPage) { "
            f"total_count items {{ {PRODUCT_FIELDS} }} "
            "} }"
        )
        yield from self._paginate(
            document=document,
            variables={"search": query},
            path=("products",),
            limit=limit,
        )

    def _scrape_category(
        self,
        slug: str,
        *,
        category: dict[str, Any] | None = None,
        limit: int | None,
    ) -> Iterable[Product]:
        category = category or self._find_category(slug, page_size=min(limit or 48, 48))
        if not category:
            return
        discovered_path = category.get("_category_path") or self._category_path_from_node(category)
        url_key = clean_text(category.get("url_key")) or slug.rstrip("/").split("/")[-1]
        document = (
            "query Category($urlKey: String!, $pageSize: Int!, $currentPage: Int!) { "
            "categoryList(filters: { url_key: { eq: $urlKey } }) { "
            "id name url_path products(pageSize: $pageSize, currentPage: $currentPage) { "
            f"total_count items {{ {PRODUCT_FIELDS} }} "
            "} } }"
        )
        categories = self._graphql(
            document,
            {"urlKey": url_key, "pageSize": 48, "currentPage": 1},
        ).get("categoryList", [])
        if not categories:
            return

        category = self._best_category_match(categories, slug)
        category["_category_path"] = discovered_path
        products = category.get("products") or {}
        for product in self._items_to_products(products.get("items") or [], fallback_category=category):
            yield product

        total = int(products.get("total_count") or 0)
        fetched = len(products.get("items") or [])
        emitted = fetched
        page = 2
        while fetched < total and (not limit or emitted < limit):
            data = self._graphql(
                document,
                {"urlKey": url_key, "pageSize": 48, "currentPage": page},
            ).get("categoryList", [])
            if not data:
                return
            page_items = (self._best_category_match(data, slug).get("products") or {}).get("items") or []
            if not page_items:
                return
            fetched += len(page_items)
            for product in self._items_to_products(page_items, fallback_category=category):
                yield product
                emitted += 1
                if limit and emitted >= limit:
                    return
            page += 1

    def _find_category(self, slug: str, *, page_size: int) -> dict[str, Any] | None:
        url_key = slug.rstrip("/").split("/")[-1]
        document = (
            "query Category($urlKey: String!, $pageSize: Int!, $currentPage: Int!) { "
            "categoryList(filters: { url_key: { eq: $urlKey } }) { "
            "id name url_key url_path products(pageSize: $pageSize, currentPage: $currentPage) { "
            f"total_count items {{ {PRODUCT_FIELDS} }} "
            "} } }"
        )
        categories = self._graphql(
            document,
            {"urlKey": url_key, "pageSize": page_size, "currentPage": 1},
        ).get("categoryList", [])
        if not categories:
            return None
        return self._best_category_match(categories, slug)

    def _paginate(
        self,
        *,
        document: str,
        variables: dict[str, Any],
        path: tuple[str, ...],
        limit: int | None,
    ) -> Iterable[Product]:
        seen: set[str] = set()
        emitted = 0
        fetched = 0
        page = 1
        total = None
        while total is None or fetched < total:
            page_size = min(limit or 48, 48)
            data = self._graphql(document, {**variables, "pageSize": page_size, "currentPage": page})
            node: dict[str, Any] = data
            for key in path:
                node = node.get(key, {})
            total = int(node.get("total_count") or 0)
            items = node.get("items") or []
            if not items:
                return
            fetched += len(items)
            for product in self._items_to_products(items):
                if product.private_key in seen:
                    continue
                seen.add(product.private_key)
                yield product
                emitted += 1
                if limit and emitted >= limit:
                    return
            page += 1

    def _graphql(self, document: str, variables: dict[str, Any]) -> dict[str, Any]:
        last_error: RuntimeError | None = None
        for attempt in range(3):
            result = self.client.post_json(
                self.graphql_url,
                {"query": document, "variables": variables},
                headers={
                    "Origin": "https://seoudisupermarket.com",
                    "Referer": self.base_url,
                },
            )
            if result.status_code >= 400:
                last_error = RuntimeError(f"Seoudi GraphQL failed with HTTP {result.status_code}")
            else:
                payload = json.loads(result.text)
                if not payload.get("errors"):
                    return payload.get("data") or {}
                last_error = RuntimeError(f"Seoudi GraphQL errors: {payload['errors']}")
            time.sleep(1 + attempt)
        raise last_error or RuntimeError("Seoudi GraphQL failed")

    def _items_to_products(
        self,
        items: list[dict[str, Any]],
        *,
        fallback_category: dict[str, Any] | None = None,
    ) -> Iterable[Product]:
        fallback_path = self._category_path_from_node(fallback_category or {})
        for item in items:
            name = clean_text(item.get("name"))
            categories = item.get("categories") or []
            category_path = self._best_product_category_path(categories) or fallback_path
            if not name or should_skip_cold_chain(name, " ".join(category_path)):
                continue

            price = (
                (item.get("price_range") or {})
                .get("minimum_price", {})
                .get("final_price", {})
            )
            description = self._html_to_text((item.get("short_description") or {}).get("html"))
            detail = self._html_to_text((item.get("description") or {}).get("html"))
            image = item.get("small_image") or {}
            url_key = item.get("url_key")
            product_url = f"{self.base_url}{url_key}" if url_key else None

            yield Product(
                source=self.name,
                source_product_id=str(item.get("id")) if item.get("id") is not None else None,
                source_sku=str(item.get("sku")) if item.get("sku") is not None else None,
                name=name,
                price=price.get("value"),
                currency=price.get("currency") or "EGP",
                image_url=image.get("url"),
                description=description or None,
                detail=detail or None,
                product_url=product_url,
                category_path=category_path,
                raw={"stock_status": item.get("stock_status")},
            )

    def _best_category_match(self, categories: list[dict[str, Any]], slug: str) -> dict[str, Any]:
        slug = slug.strip("/")
        for category in categories:
            if clean_text(category.get("url_path")).strip("/") == slug:
                return category
        return categories[0]

    def _best_product_category_path(self, categories: list[dict[str, Any]]) -> list[str]:
        if not categories:
            return []
        ranked = sorted(categories, key=lambda item: len(clean_text(item.get("url_path")).split("/")), reverse=True)
        return self._category_path_from_node(ranked[0])

    def _collect_category_leaves(
        self,
        node: dict[str, Any],
        parents: list[str],
        leaves: list[dict[str, Any]],
    ) -> None:
        name = clean_text(node.get("name"))
        path = [*parents, name] if name else parents
        if should_skip_cold_chain(" ".join(path), clean_text(node.get("url_path"))):
            return
        children = [
            child for child in node.get("children") or []
            if int(child.get("product_count") or 0) > 0
        ]
        if not children and int(node.get("product_count") or 0) > 0 and clean_text(node.get("url_key")):
            item = {**node, "_category_path": path}
            leaves.append(item)
            return
        for child in children:
            self._collect_category_leaves(child, path, leaves)

    def _category_path_from_node(self, node: dict[str, Any]) -> list[str]:
        if node.get("_category_path"):
            return [clean_text(part) for part in node["_category_path"] if clean_text(part)]
        names = [clean_text(node.get("name"))] if node.get("name") else []
        if names:
            return names
        return [part.replace("-", " ").title() for part in clean_text(node.get("url_path")).split("/") if part]

    def _html_to_text(self, html: str | None) -> str:
        if not html:
            return ""
        soup = BeautifulSoup(unescape(html), "lxml")
        return clean_text(soup.get_text(" "))

    @staticmethod
    def category_urls_from_html(html: str) -> list[str]:
        return sorted(set(re.findall(r'href="(/en/[^"]+)"', html)))
