from __future__ import annotations

import json
import re
from collections.abc import Iterable
from urllib.parse import quote_plus

from bs4 import BeautifulSoup

from ..models import Product
from ..normalize import absolute_url, clean_text, parse_price, should_skip_cold_chain
from .base import Source


class GourmetSource(Source):
    name = "gourmet"
    base_url = "https://gourmetegypt.com/"

    def scrape(self, *, query: str | None = None, limit: int | None = None) -> Iterable[Product]:
        urls = [f"{self.base_url}catalogsearch/result/?q={quote_plus(query or 'water')}"]
        seen: set[str] = set()
        count = 0
        for url in urls:
            result = self.client.get(url)
            for product in self._parse_listing(result.text, result.url):
                key = product.private_key
                if key in seen:
                    continue
                seen.add(key)
                yield product
                count += 1
                if limit and count >= limit:
                    return

    def _parse_listing(self, html: str, page_url: str) -> Iterable[Product]:
        data_layer_products = list(self._parse_datalayer_items(html, page_url))
        if data_layer_products:
            yield from data_layer_products
            return

        soup = BeautifulSoup(html, "lxml")
        category_path = self._category_from_datalayer(html)

        for card in soup.select("li.product-item"):
            sku = card.get("data-sku-id")
            name_link = card.select_one(".product-item-link")
            name = clean_text(name_link.get("title") if name_link else card.get_text(" "))
            if not name:
                continue
            if should_skip_cold_chain(name, " ".join(category_path)):
                continue

            product_url = absolute_url(page_url, name_link.get("href") if name_link else None)
            image = card.select_one("img.product-image-photo")
            image_url = None
            if image:
                image_url = image.get("data-src") or image.get("src")
                image_url = absolute_url(page_url, image_url)

            price_node = card.select_one("[data-price-amount]") or card.select_one(".price")
            price = parse_price(price_node.get("data-price-amount") if price_node else None)
            if price is None and price_node:
                price = parse_price(price_node.get_text(" "))

            source_product_id = None
            product_input = card.select_one('input[name="product"]')
            if product_input:
                source_product_id = product_input.get("value")
            if not source_product_id:
                container = card.select_one('[class*="product-image-container-"]')
                if container:
                    match = re.search(r"product-image-container-(\d+)", " ".join(container.get("class", [])))
                    source_product_id = match.group(1) if match else None

            yield Product(
                source=self.name,
                source_product_id=source_product_id,
                source_sku=sku,
                name=name,
                price=price,
                image_url=image_url,
                product_url=product_url,
                category_path=category_path,
                raw={"page_url": page_url},
            )

    def _parse_datalayer_items(self, html: str, page_url: str) -> Iterable[Product]:
        match = re.search(r"var\s+dl4Objects\s*=\s*(\[.*?\]);", html, re.S)
        if not match:
            return
        try:
            events = json.loads(match.group(1))
        except json.JSONDecodeError:
            return

        for event in events:
            for item in event.get("ecommerce", {}).get("items", []):
                name = clean_text(item.get("item_name"))
                category_path = [
                    clean_text(item.get(key))
                    for key in ("item_category", "item_category_2", "item_category_3")
                    if clean_text(item.get(key))
                ]
                if not name or should_skip_cold_chain(name, " ".join(category_path)):
                    continue
                yield Product(
                    source=self.name,
                    source_product_id=str(item.get("item_id")) if item.get("item_id") is not None else None,
                    source_sku=None,
                    name=name,
                    price=parse_price(str(item.get("price"))) if item.get("price") is not None else None,
                    currency=item.get("currency") or "EGP",
                    image_url=None,
                    product_url=None,
                    category_path=category_path,
                    raw={"page_url": page_url, "data_layer": item},
                )

    def _category_from_datalayer(self, html: str) -> list[str]:
        categories: list[str] = []
        for name in ("item_category", "item_category_2", "item_category_3"):
            match = re.search(rf'"{name}"\s*:\s*"([^"]+)"', html)
            if match:
                categories.append(clean_text(match.group(1)))
        return categories
