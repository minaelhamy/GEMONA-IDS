from __future__ import annotations

import json
import sys
from collections.abc import Iterable
from html import unescape
from typing import Any

from bs4 import BeautifulSoup

from ..models import Product
from ..normalize import clean_text, should_skip_cold_chain
from ..settings import DEFAULT_LOCATION
from .base import Source


PRODUCT_FIELDS = """
id
uid
sku
name
url_key
thumbnail { url label }
categories { id name url_path level section }
price_range {
  minimum_price {
    final_price { value currency }
    regular_price { value currency }
  }
  maximum_price {
    final_price { value currency }
    regular_price { value currency }
  }
}
short_description { html }
description { html }
"""


class HyperOneSource(Source):
    name = "hyperone"
    base_url = "https://www.hyperone.com.eg/en/"
    graphql_url = "https://mcprod.hyperone.com.eg/graphql"

    def scrape(self, *, query: str | None = None, limit: int | None = None) -> Iterable[Product]:
        query = clean_text(query) or "water"
        source_code = self._source_code()
        yield from self._scrape_search(query, source_code=source_code, limit=limit)

    def crawl(
        self,
        *,
        limit: int | None = None,
        limit_categories: int | None = None,
    ) -> Iterable[Product]:
        source_code = self._source_code()
        categories = self.discover_categories(source_code=source_code)
        if limit_categories:
            categories = categories[:limit_categories]
        seen: set[str] = set()
        emitted = 0
        for category in categories:
            try:
                category_products = self._scrape_category(category, source_code=source_code, limit=None)
                for product in category_products:
                    if product.private_key in seen:
                        continue
                    seen.add(product.private_key)
                    yield product
                    emitted += 1
                    if limit and emitted >= limit:
                        return
            except RuntimeError as exc:
                label = clean_text(category.get("url_path") or category.get("name") or str(category.get("id")))
                print(f"hyperone: skipped category {label}: {exc}", file=sys.stderr, flush=True)
                continue

    def discover_categories(self, *, source_code: str | None = None) -> list[dict[str, Any]]:
        document = """
        query CategoryList {
          categories {
            items {
              id name uid url_key path url_path include_in_menu section children {
                id name uid url_key path url_path include_in_menu section children {
                  id name uid url_key path url_path include_in_menu section children {
                    id name uid url_key path url_path include_in_menu section children {
                      id name uid url_key path url_path include_in_menu section
                    }
                  }
                }
              }
            }
          }
        }
        """
        data = self._graphql(document, {}, source_code=source_code or self._source_code())
        leaves: list[dict[str, Any]] = []
        for root in ((data.get("categories") or {}).get("items") or []):
            self._collect_category_leaves(root, [], leaves)
        unique: dict[str, dict[str, Any]] = {}
        for category in leaves:
            key = str(category.get("id") or category.get("url_path") or category.get("url_key"))
            unique[key] = category
        return list(unique.values())

    def _source_code(self) -> str:
        document = """
        query FindStore($lat: String!, $lng: String!) {
          findStore(lat: $lat, lng: $lng) {
            district {
              store: available_store {
                storeSource { source_code }
              }
            }
          }
        }
        """
        data = self._graphql(
            document,
            {"lat": str(DEFAULT_LOCATION["lat"]), "lng": str(DEFAULT_LOCATION["lng"])},
            source_code="default",
        )
        district = (data.get("findStore") or {}).get("district") or {}
        store = district.get("store") or {}
        store_source = store.get("storeSource") or {}
        return clean_text(store_source.get("source_code")) or "ZAYD"

    def _scrape_search(
        self,
        query: str,
        *,
        source_code: str,
        limit: int | None,
    ) -> Iterable[Product]:
        document = (
            "query Products($search: String!, $pageSize: Int!, $currentPage: Int!) { "
            "products(search: $search, pageSize: $pageSize, currentPage: $currentPage) { "
            f"total_count items {{ {PRODUCT_FIELDS} }} "
            "} }"
        )
        yield from self._paginate(
            document=document,
            variables={"search": query},
            source_code=source_code,
            limit=limit,
        )

    def _scrape_category(
        self,
        category: dict[str, Any],
        *,
        source_code: str,
        limit: int | None,
    ) -> Iterable[Product]:
        document = (
            "query Products($categoryId: String!, $pageSize: Int!, $currentPage: Int!) { "
            "products(filter: { category_id: { eq: $categoryId } }, pageSize: $pageSize, currentPage: $currentPage) { "
            f"total_count items {{ {PRODUCT_FIELDS} }} "
            "} }"
        )
        yield from self._paginate(
            document=document,
            variables={"categoryId": str(category["id"])},
            source_code=source_code,
            limit=limit,
            fallback_category=category,
        )

    def _paginate(
        self,
        *,
        document: str,
        variables: dict[str, Any],
        source_code: str,
        limit: int | None,
        fallback_category: dict[str, Any] | None = None,
    ) -> Iterable[Product]:
        seen: set[str] = set()
        emitted = 0
        fetched = 0
        page = 1
        total = None
        while total is None or fetched < total:
            page_size = min(limit or 48, 48)
            data = self._graphql(
                document,
                {**variables, "pageSize": page_size, "currentPage": page},
                source_code=source_code,
            )
            products = data.get("products") or {}
            total = int(products.get("total_count") or 0)
            items = products.get("items") or []
            if not items:
                return
            fetched += len(items)
            for product in self._items_to_products(items, fallback_category=fallback_category):
                if product.private_key in seen:
                    continue
                seen.add(product.private_key)
                yield product
                emitted += 1
                if limit and emitted >= limit:
                    return
            page += 1

    def _graphql(self, document: str, variables: dict[str, Any], *, source_code: str) -> dict[str, Any]:
        result = self.client.post_json(
            f"{self.graphql_url}?source={source_code}",
            {"query": document, "variables": variables},
            headers={
                "Origin": "https://www.hyperone.com.eg",
                "Referer": self.base_url,
            },
        )
        if result.status_code >= 400:
            raise RuntimeError(f"HyperOne GraphQL failed with HTTP {result.status_code}")
        payload = json.loads(result.text)
        if payload.get("errors"):
            raise RuntimeError(f"HyperOne GraphQL errors: {payload['errors']}")
        return payload.get("data") or {}

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
            thumbnail = item.get("thumbnail") or {}
            url_key = clean_text(item.get("url_key"))

            yield Product(
                source=self.name,
                source_product_id=str(item.get("id")) if item.get("id") is not None else None,
                source_sku=str(item.get("sku")) if item.get("sku") is not None else None,
                name=name,
                price=price.get("value"),
                currency=price.get("currency") or "EGP",
                image_url=thumbnail.get("url"),
                description=self._html_to_text((item.get("short_description") or {}).get("html")) or None,
                detail=self._html_to_text((item.get("description") or {}).get("html")) or None,
                product_url=f"{self.base_url}{url_key}" if url_key else None,
                category_path=category_path,
                raw={"uid": item.get("uid")},
            )

    def _collect_category_leaves(
        self,
        node: dict[str, Any],
        parents: list[str],
        leaves: list[dict[str, Any]],
    ) -> None:
        name = clean_text(node.get("name"))
        if name.lower() == "default category":
            path = parents
        else:
            path = [*parents, name] if name else parents
        if should_skip_cold_chain(" ".join(path), clean_text(node.get("url_path")), clean_text(node.get("section"))):
            return
        children = node.get("children") or []
        if not children and node.get("id") is not None:
            leaves.append({**node, "_category_path": path})
            return
        for child in children:
            self._collect_category_leaves(child, path, leaves)

    def _best_product_category_path(self, categories: list[dict[str, Any]]) -> list[str]:
        if not categories:
            return []
        ranked = sorted(categories, key=lambda item: int(item.get("level") or 0), reverse=True)
        return self._category_path_from_node(ranked[0])

    def _category_path_from_node(self, node: dict[str, Any]) -> list[str]:
        if node.get("_category_path"):
            return [clean_text(part) for part in node["_category_path"] if clean_text(part)]
        if node.get("name"):
            return [clean_text(node.get("name"))]
        return [part.replace("-", " ").title() for part in clean_text(node.get("url_path")).split("/") if part]

    def _html_to_text(self, html: str | None) -> str:
        if not html:
            return ""
        soup = BeautifulSoup(unescape(html), "lxml")
        return clean_text(soup.get_text(" "))
