from __future__ import annotations

import json
import re
import sys
from collections.abc import Iterable
from typing import Any
from urllib.parse import quote_plus

from bs4 import BeautifulSoup

from ..models import Product
from ..normalize import absolute_url, clean_text, parse_price, should_skip_cold_chain
from ..settings import DEFAULT_LOCATION
from .base import Source


class CarrefourSource(Source):
    name = "carrefour"
    base_url = "https://www.carrefouregypt.com/"
    storefront_url = "https://www.carrefouregypt.com/mafegy/en/"
    api_url = "https://www.carrefouregypt.com/api"
    store_id = "mafegy"
    product_api_version = "v8"
    fallback_categories = [
        {"id": "FEGY1720000", "name": "Breakfast Cereals & Bars", "_category_path": ["Food Cupboard", "Breakfast Cereals & Bars"]},
        {"id": "FEGY1730000", "name": "Chips, Dips & Snacks", "_category_path": ["Food Cupboard", "Chips, Dips & Snacks"]},
        {"id": "FEGY1740000", "name": "Chocolate & Confectionery", "_category_path": ["Food Cupboard", "Chocolate & Confectionery"]},
        {"id": "FEGY1760000", "name": "Cooking Ingredients", "_category_path": ["Food Cupboard", "Cooking Ingredients"]},
        {"id": "FEGY1780000", "name": "Nuts, Dates & Dried Fruits", "_category_path": ["Food Cupboard", "Nuts, Dates & Dried Fruits"]},
        {"id": "FEGY1790000", "name": "World Specialities", "_category_path": ["Food Cupboard", "World Specialities"]},
        {"id": "FEGY1701200", "name": "Rice, Pasta & Pulses", "_category_path": ["Food Cupboard", "Rice, Pasta & Pulses"]},
        {"id": "FEGY1701300", "name": "Sugar & Home Baking", "_category_path": ["Food Cupboard", "Sugar & Home Baking"]},
        {"id": "FEGY1750000", "name": "Condiments, Dressings & Marinades", "_category_path": ["Food Cupboard", "Condiments, Dressings & Marinades"]},
        {"id": "FEGY1770000", "name": "Jams, Honey & Spreads", "_category_path": ["Food Cupboard", "Jams, Honey & Spreads"]},
        {"id": "FEGY1714000", "name": "Tins, Jars & Packets", "_category_path": ["Food Cupboard", "Tins, Jars & Packets"]},
        {"id": "FEGY1710000", "name": "Biscuits, Crackers & Cakes", "_category_path": ["Food Cupboard", "Biscuits, Crackers & Cakes"]},
        {"id": "FEGY1570000", "name": "Water", "_category_path": ["Beverages", "Water"]},
        {"id": "FEGY1510000", "name": "Coffee", "_category_path": ["Beverages", "Coffee"]},
        {"id": "FEGY1560000", "name": "Tea", "_category_path": ["Beverages", "Tea"]},
        {"id": "FEGY1550000", "name": "Soft Drinks", "_category_path": ["Beverages", "Soft Drinks"]},
        {"id": "FEGY1520000", "name": "Juices", "_category_path": ["Beverages", "Juices"]},
        {"id": "FEGY1540000", "name": "Powdered Drinks", "_category_path": ["Beverages", "Powdered Drinks"]},
        {"id": "FEGY1530000", "name": "Kids Drinks", "_category_path": ["Beverages", "Kids Drinks"]},
        {"id": "NFEGY3020000", "name": "Cleaning Supplies", "_category_path": ["Cleaning & Household", "Cleaning Supplies"]},
        {"id": "NFEGY3060000", "name": "Garbage Bags", "_category_path": ["Cleaning & Household", "Garbage Bags"]},
        {"id": "NFEGY3080000", "name": "Laundry & Detergents", "_category_path": ["Cleaning & Household", "Laundry & Detergents"]},
        {"id": "NFEGY3090000", "name": "Tissues", "_category_path": ["Cleaning & Household", "Tissues"]},
        {"id": "NFEGY3010000", "name": "Candles & Air Fresheners", "_category_path": ["Cleaning & Household", "Candles & Air Fresheners"]},
        {"id": "NFEGY3040000", "name": "Food Storage, Foil & Cling Film", "_category_path": ["Cleaning & Household", "Food Storage, Foil & Cling Film"]},
        {"id": "NFEGY3070000", "name": "Insect & Pest Control", "_category_path": ["Cleaning & Household", "Insect & Pest Control"]},
        {"id": "NFEGY3100000", "name": "Kitchen & Toilet Rolls", "_category_path": ["Cleaning & Household", "Kitchen & Toilet Rolls"]},
        {"id": "NFEGY3030000", "name": "Disposables Tableware & Napkins", "_category_path": ["Cleaning & Household", "Disposables Tableware & Napkins"]},
    ]

    def products_from_html(self, html: str, *, category_path: list[str] | None = None) -> Iterable[Product]:
        embedded_products = list(self._products_from_embedded_cards(html, category_path=category_path))
        if embedded_products:
            yield from embedded_products
            return

        soup = BeautifulSoup(html, "lxml")
        lines = [clean_text(item) for item in soup.get_text("\n").splitlines()]
        lines = [line for line in lines if line]
        category_path = category_path or self._category_path_from_lines(lines)
        for index, line in enumerate(lines):
            link = self._product_link_for_text(soup, line)
            product_id = self._product_id_from_url(link)
            if not product_id:
                continue
            price = self._price_near_lines(lines[index + 1 : index + 8])
            if price is None or should_skip_cold_chain(line, " ".join(category_path)):
                continue
            image_url = self._image_for_text(soup, line)
            yield Product(
                source=self.name,
                source_product_id=product_id,
                source_sku=None,
                name=line,
                price=price,
                currency="EGP",
                image_url=image_url,
                description=None,
                detail=None,
                product_url=absolute_url(self.base_url, link),
                category_path=category_path,
                raw={"html_snapshot": True},
            )

    def scrape(self, *, query: str | None = None, limit: int | None = None) -> Iterable[Product]:
        query = clean_text(query) or "water"
        yield from self._search(query, limit=limit)

    def crawl(
        self,
        *,
        limit: int | None = None,
        limit_categories: int | None = None,
    ) -> Iterable[Product]:
        categories = self.discover_categories()
        if limit_categories:
            categories = categories[:limit_categories]
        seen: set[str] = set()
        emitted = 0
        for category in categories:
            try:
                category_products = self._crawl_category_html(category, limit=None)
                for product in category_products:
                    if product.private_key in seen:
                        continue
                    seen.add(product.private_key)
                    yield product
                    emitted += 1
                    if limit and emitted >= limit:
                        return
            except RuntimeError as exc:
                try:
                    category_products = self._crawl_category(category, limit=None)
                    for product in category_products:
                        if product.private_key in seen:
                            continue
                        seen.add(product.private_key)
                        yield product
                        emitted += 1
                        if limit and emitted >= limit:
                            return
                except RuntimeError as api_exc:
                    label = clean_text(category.get("name") or str(category.get("id")))
                    print(f"carrefour: skipped category {label}: html={exc}; api={api_exc}", file=sys.stderr, flush=True)
                    continue

    def discover_categories(self) -> list[dict[str, Any]]:
        try:
            categories = self._discover_categories_from_html()
            if categories:
                return categories
        except Exception:
            pass

        try:
            data = self._get(
                "v1/menu",
                {
                    "latitude": DEFAULT_LOCATION["lat"],
                    "longitude": DEFAULT_LOCATION["lng"],
                    "lang": "en",
                    "displayCurr": "EGP",
                },
            )
        except Exception:
            return self.fallback_categories

        leaves: list[dict[str, Any]] = []
        roots = data.get("items") or data.get("categories") or data.get("data") or []
        for node in roots:
            self._collect_category_leaves(node, [], leaves)
        return leaves or self.fallback_categories

    def _discover_categories_from_html(self) -> list[dict[str, Any]]:
        html = self._get_html(f"{self.storefront_url}all-categories", require_product_cards=False)
        soup = BeautifulSoup(html, "lxml")
        categories: dict[str, dict[str, Any]] = {}
        ignored_labels = {
            "view all",
            "home",
            "categories",
            "profile",
            "cart",
            "login & register",
            "chat with us for assistance",
            "myclub program",
            "find a store",
        }
        for anchor in soup.find_all("a"):
            name = clean_text(anchor.get_text(" "))
            href = str(anchor.get("href") or "")
            match = re.search(r"/c/([^/?#]+)", href)
            if not name or not match or name.lower() in ignored_labels:
                continue
            if should_skip_cold_chain(name, href):
                continue
            category_path = self._category_path_for_anchor(anchor, name)
            if should_skip_cold_chain(" ".join(category_path)):
                continue
            category_id = match.group(1)
            categories[category_id] = {
                "id": category_id,
                "name": name,
                "_category_path": category_path,
            }
        return list(categories.values())

    def _category_path_for_anchor(self, anchor: Any, name: str) -> list[str]:
        section = anchor.find_parent("div", class_="mt-lg")
        if section:
            parts = [clean_text(part) for part in section.get_text("\n").splitlines()]
            parts = [part for part in parts if part]
            if "View All" in parts:
                parent = clean_text(" ".join(parts[: parts.index("View All")]))
                if parent and parent != name:
                    return [parent, name]
        return [name]

    def _search(self, query: str, *, limit: int | None) -> Iterable[Product]:
        page = 0
        emitted = 0
        seen: set[str] = set()
        while True:
            data = self._get(
                f"{self.product_api_version}/products/search",
                {
                    "keyword": query,
                    "filter": "",
                    "sortBy": "relevance",
                    "currentPage": page,
                    "pageSize": min(limit or 60, 60),
                    "areaCode": "Maadi - Cairo",
                    "lang": "en",
                    "displayCurr": "EGP",
                    "latitude": DEFAULT_LOCATION["lat"],
                    "longitude": DEFAULT_LOCATION["lng"],
                    "nextOffset": page * 60,
                    "requireSponsProducts": "true",
                    "needVariantsData": "true",
                },
            )
            items = self._products_from_payload(data)
            if not items:
                return
            for item in items:
                product = self._product_from_item(item)
                if product is None or product.private_key in seen:
                    continue
                seen.add(product.private_key)
                yield product
                emitted += 1
                if limit and emitted >= limit:
                    return
            if emitted >= int(data.get("totalResults") or data.get("total_count") or emitted):
                return
            page += 1

    def _crawl_category(self, category: dict[str, Any], *, limit: int | None) -> Iterable[Product]:
        page = 0
        emitted = 0
        while True:
            data = self._get(
                f"{self.product_api_version}/categories/{quote_plus(str(category['id']))}",
                {
                    "filter": "",
                    "sortBy": "relevance",
                    "currentPage": page,
                    "pageSize": min(limit or 60, 60),
                    "maxPrice": "",
                    "minPrice": "",
                    "areaCode": "Maadi - Cairo",
                    "lang": "en",
                    "displayCurr": "EGP",
                    "latitude": DEFAULT_LOCATION["lat"],
                    "longitude": DEFAULT_LOCATION["lng"],
                    "nextOffset": page * 60,
                    "requireSponsProducts": "true",
                    "responseWithCatTree": "true",
                    "needVariantsData": "true",
                    "depth": 3,
                },
            )
            items = self._products_from_payload(data)
            if not items:
                return
            for item in items:
                product = self._product_from_item(item, category_path=category.get("_category_path") or [])
                if product:
                    yield product
                    emitted += 1
                    if limit and emitted >= limit:
                        return
            if emitted >= int(data.get("totalResults") or data.get("total_count") or emitted):
                return
            page += 1

    def _crawl_category_html(self, category: dict[str, Any], *, limit: int | None) -> Iterable[Product]:
        page = 0
        emitted = 0
        total_pages = None
        while total_pages is None or page < total_pages:
            url = self._category_url(category, current_page=page)
            html = self._get_html(url)
            total_pages = self._total_pages_from_html(html) or 1
            products = list(self.products_from_html(html, category_path=category.get("_category_path") or [category["name"]]))
            if not products:
                return
            for product in products:
                yield product
                emitted += 1
                if limit and emitted >= limit:
                    return
            page += 1

    def _category_url(self, category: dict[str, Any], *, current_page: int) -> str:
        suffix = f"?currentPage={current_page}" if current_page else ""
        return f"{self.storefront_url}c/{quote_plus(str(category['id']))}{suffix}"

    def _get_html(self, url: str, *, require_product_cards: bool = True) -> str:
        try:
            from curl_cffi import requests as curl_requests
        except ModuleNotFoundError as exc:
            raise RuntimeError("curl_cffi is required for Carrefour HTML category fetching") from exc

        response = curl_requests.get(
            url,
            impersonate="chrome120",
            timeout=30,
            headers={
                "Accept": "text/html,application/xhtml+xml,application/xml;q=0.9,*/*;q=0.8",
                "Accept-Language": "en-US,en;q=0.9,ar;q=0.8",
            },
        )
        if response.status_code >= 400:
            raise RuntimeError(f"Carrefour HTML {url} failed with HTTP {response.status_code}")
        text = response.text
        if require_product_cards and "productName" not in text and "master-product-card" not in text:
            raise RuntimeError(f"Carrefour HTML {url} did not contain product cards")
        return text

    def _get(self, path: str, params: dict[str, Any]) -> dict[str, Any]:
        query = "&".join(f"{key}={quote_plus(str(value))}" for key, value in params.items())
        result = self.client.get(
            f"{self.api_url}/{path}?{query}",
            headers={
                "Accept": "application/json",
                "Referer": self.storefront_url,
                "Origin": self.base_url.rstrip("/"),
                "storeId": self.store_id,
                "appId": "Reactweb",
                "lang": "en",
                "langCode": "en",
                "userId": "anonymous",
                "X-Requested-With": "XMLHttpRequest",
            },
        )
        if result.status_code >= 400:
            raise RuntimeError(f"Carrefour API {path} failed with HTTP {result.status_code}")
        try:
            return json.loads(result.text)
        except json.JSONDecodeError as exc:
            snippet = clean_text(result.text[:200])
            raise RuntimeError(
                f"Carrefour API {path} returned non-JSON or empty response "
                f"(HTTP {result.status_code}, content-type {result.headers.get('content-type', 'unknown')}): {snippet}"
            ) from exc

    def _products_from_payload(self, data: dict[str, Any]) -> list[dict[str, Any]]:
        for key in ("products", "items", "results"):
            value = data.get(key)
            if isinstance(value, list):
                return value
        nested = data.get("data")
        if isinstance(nested, dict):
            return self._products_from_payload(nested)
        return []

    def _products_from_embedded_cards(
        self,
        html: str,
        *,
        category_path: list[str] | None = None,
    ) -> Iterable[Product]:
        decoded = self._decode_next_payload(html)
        for item in self._embedded_additional_attributes(decoded):
            product = self._product_from_embedded_attributes(item, category_path=category_path)
            if product:
                yield product

    def _decode_next_payload(self, html: str) -> str:
        return (
            html
            .replace('\\"', '"')
            .replace("\\u0026", "&")
            .replace("\\/", "/")
        )

    def _embedded_additional_attributes(self, decoded: str) -> Iterable[dict[str, Any]]:
        marker = '"additionalAttributes":{"orderThreshold"'
        start = 0
        while True:
            index = decoded.find(marker, start)
            if index < 0:
                return
            object_start = decoded.find("{", index + len('"additionalAttributes":') - 1)
            object_end = self._balanced_object_end(decoded, object_start)
            if object_end is None:
                start = index + 1
                continue
            try:
                yield json.loads(decoded[object_start:object_end])
            except json.JSONDecodeError:
                pass
            start = object_end

    def _balanced_object_end(self, text: str, start: int) -> int | None:
        if start < 0 or start >= len(text) or text[start] != "{":
            return None
        depth = 0
        in_string = False
        escaped = False
        for index in range(start, len(text)):
            char = text[index]
            if in_string:
                if escaped:
                    escaped = False
                elif char == "\\":
                    escaped = True
                elif char == '"':
                    in_string = False
            else:
                if char == '"':
                    in_string = True
                elif char == "{":
                    depth += 1
                elif char == "}":
                    depth -= 1
                    if depth == 0:
                        return index + 1
        return None

    def _product_from_embedded_attributes(
        self,
        item: dict[str, Any],
        *,
        category_path: list[str] | None = None,
    ) -> Product | None:
        name = clean_text(item.get("productName"))
        category_path = category_path or [clean_text(part) for part in item.get("productCategory") or [] if clean_text(part)]
        if not name or should_skip_cold_chain(name, " ".join(category_path)):
            return None
        product_id = clean_text(item.get("productId"))
        return Product(
            source=self.name,
            source_product_id=product_id or None,
            source_sku=None,
            name=name,
            price=parse_price(str(item.get("sellingPrice"))),
            currency=item.get("currency") or "EGP",
            image_url=item.get("imageUrl"),
            description=None,
            detail=None,
            product_url=absolute_url(self.base_url, item.get("productUrl")),
            category_path=category_path,
            raw={
                "stock": item.get("stock"),
                "marked_price": item.get("markedPrice"),
                "product_type": item.get("productType"),
                "html_embedded": True,
            },
        )

    def _total_pages_from_html(self, html: str) -> int | None:
        decoded = self._decode_next_payload(html)
        match = re.search(r'"pagination":\{"pageSize":\d+,"totalPages":(\d+),', decoded)
        return int(match.group(1)) if match else None

    def _category_path_from_lines(self, lines: list[str]) -> list[str]:
        if "#" in lines:
            index = lines.index("#")
            if index + 1 < len(lines):
                return [lines[index + 1]]
        for marker in ("Sort by: Relevance", "Filters"):
            if marker in lines:
                index = lines.index(marker)
                for candidate in reversed(lines[:index]):
                    if candidate not in {"Home", "All Categories", "Next"}:
                        return [candidate]
        return []

    def _product_link_for_text(self, soup: BeautifulSoup, text: str) -> str | None:
        for anchor in soup.find_all("a"):
            if clean_text(anchor.get_text(" ")) == text:
                href = anchor.get("href")
                if href and "/p/" in href:
                    return str(href)
        return None

    def _product_id_from_url(self, url: str | None) -> str | None:
        if not url:
            return None
        match = re.search(r"/p/([^/?#]+)", url)
        return match.group(1) if match else None

    def _price_near_lines(self, lines: list[str]) -> float | None:
        for offset, line in enumerate(lines[:-2]):
            if lines[offset + 2] != "EGP":
                continue
            whole = line.replace(",", "")
            fraction = lines[offset + 1]
            if whole.isdigit() and re.fullmatch(r"\.\d{2}", fraction):
                return float(f"{whole}{fraction}")
        return None

    def _image_for_text(self, soup: BeautifulSoup, text: str) -> str | None:
        image = soup.find("img", attrs={"alt": text})
        if image:
            value = image.get("src") or image.get("data-src")
            if value:
                return absolute_url(self.base_url, str(value))
        return None

    def _product_from_item(
        self,
        item: dict[str, Any],
        *,
        category_path: list[str] | None = None,
    ) -> Product | None:
        name = clean_text(item.get("name") or item.get("title"))
        category_path = category_path or self._category_path_from_item(item)
        if not name or should_skip_cold_chain(name, " ".join(category_path)):
            return None
        price_node = item.get("price") or {}
        if isinstance(price_node, dict):
            price_value = (
                price_node.get("value")
                or price_node.get("formattedValue")
                or ((price_node.get("discount") or {}).get("value"))
            )
            currency = price_node.get("currencyIso") or price_node.get("currency") or "EGP"
        else:
            price_value = price_node
            currency = "EGP"
        links = item.get("links") or {}
        product_link = (links.get("productUrl") or {}).get("href") if isinstance(links.get("productUrl"), dict) else None
        images = item.get("images") or item.get("image") or []
        image_url = self._image_url(images)
        product_id = (
            item.get("id")
            or item.get("code")
            or item.get("productId")
            or item.get("ean")
            or item.get("sku")
        )
        return Product(
            source=self.name,
            source_product_id=str(product_id) if product_id is not None else None,
            source_sku=str(item.get("ean") or item.get("barcode") or item.get("sku") or "") or None,
            name=name,
            price=parse_price(str(price_value)),
            currency=currency,
            image_url=image_url,
            description=clean_text(item.get("description") or item.get("summary")) or None,
            detail=self._detail(item),
            product_url=absolute_url(self.base_url, product_link),
            category_path=category_path,
            raw={
                "brand": (item.get("brand") or {}).get("name") if isinstance(item.get("brand"), dict) else item.get("brand"),
                "availability": item.get("availability"),
            },
        )

    def _image_url(self, images: Any) -> str | None:
        if isinstance(images, str):
            return absolute_url(self.base_url, images)
        if isinstance(images, dict):
            return absolute_url(self.base_url, images.get("url") or images.get("href"))
        if isinstance(images, list):
            for image in images:
                if isinstance(image, dict):
                    url = image.get("url") or image.get("href")
                    if url:
                        return absolute_url(self.base_url, url)
        return None

    def _detail(self, item: dict[str, Any]) -> str | None:
        parts = []
        for key in ("size", "packing", "originCountry"):
            value = item.get(key)
            if isinstance(value, dict):
                value = value.get("name") or value.get("value")
            text = clean_text(str(value) if value is not None else "")
            if text:
                parts.append(f"{key}: {text}")
        return "; ".join(parts) or None

    def _category_path_from_item(self, item: dict[str, Any]) -> list[str]:
        categories = item.get("categories") or item.get("category") or []
        if isinstance(categories, dict):
            categories = [categories]
        path = []
        for category in categories:
            if isinstance(category, dict):
                name = clean_text(category.get("name"))
                if name:
                    path.append(name)
        return path

    def _collect_category_leaves(
        self,
        node: dict[str, Any],
        parents: list[str],
        leaves: list[dict[str, Any]],
    ) -> None:
        name = clean_text(node.get("name") or node.get("title"))
        category_id = node.get("id") or node.get("code") or node.get("urlKey") or node.get("slug")
        path = [*parents, name] if name else parents
        if should_skip_cold_chain(" ".join(path)):
            return
        children = node.get("children") or node.get("subCategories") or []
        if not children and category_id:
            leaves.append({"id": category_id, "name": name, "_category_path": path})
            return
        for child in children:
            if isinstance(child, dict):
                self._collect_category_leaves(child, path, leaves)
