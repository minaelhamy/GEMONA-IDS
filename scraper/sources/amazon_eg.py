from __future__ import annotations

import sys
from collections.abc import Iterable
from typing import Any
from urllib.parse import quote_plus

from bs4 import BeautifulSoup

from ..http import HttpClient
from ..models import Product
from ..normalize import absolute_url, clean_text, parse_price, should_skip_cold_chain
from .base import Source


FBA_REFINEMENT = "p_98:21909049031"

AMAZON_EG_CATEGORIES = [
    {
        "name": "Mobiles, Tablets & Accessories",
        "keywords": ["mobiles tablets accessories", "mobile phones", "tablets"],
        "department": "electronics",
        "nodes": ["21832868031", "21832883031"],
    },
    {
        "name": "Computers & Office Supplies",
        "keywords": ["computers office supplies", "laptops", "office supplies"],
        "department": "computers",
        "nodes": [],
    },
    {
        "name": "TVs & Electronics",
        "keywords": ["tv electronics", "television", "electronics"],
        "department": "electronics",
        "nodes": [],
    },
    {
        "name": "Women's Fashion",
        "keywords": ["women fashion", "women clothing", "women shoes"],
        "department": "fashion",
        "nodes": [],
    },
    {
        "name": "Men's Fashion",
        "keywords": ["men fashion", "men clothing", "men shoes"],
        "department": "fashion",
        "nodes": [],
    },
    {
        "name": "Kids Fashion",
        "keywords": ["kids fashion", "kids clothing", "kids shoes"],
        "department": "fashion",
        "nodes": [],
    },
    {
        "name": "Health, Beauty & Perfumes",
        "keywords": ["health beauty perfumes", "beauty", "perfume"],
        "department": "beauty",
        "nodes": ["18017988031"],
    },
    {
        "name": "Supermarket",
        "keywords": ["supermarket grocery", "grocery", "food"],
        "department": "grocery",
        "nodes": [],
    },
    {
        "name": "Home, Furniture & Tools",
        "keywords": ["home furniture tools", "furniture", "tools"],
        "department": None,
        "nodes": ["18021933031"],
    },
    {
        "name": "Kitchen & Appliances",
        "keywords": ["kitchen appliances", "kitchen", "appliances"],
        "department": "kitchen",
        "nodes": ["18021933031"],
    },
    {
        "name": "Toys, Games & Baby",
        "keywords": ["toys games baby", "toys", "baby toys"],
        "department": "toys",
        "nodes": [],
    },
    {
        "name": "Sports, Fitness & Outdoors",
        "keywords": ["sports fitness outdoors", "sports", "fitness"],
        "department": "sporting",
        "nodes": [],
    },
]


class AmazonEgSource(Source):
    name = "amazon_eg"
    base_url = "https://www.amazon.eg"
    max_pages_per_seed = 10

    def __init__(self, client: HttpClient | None = None) -> None:
        super().__init__(client=client or HttpClient(delay_seconds=1.5))
        self.client.session.headers.update(
            {
                "Accept-Encoding": "gzip, deflate, br",
                "Accept-Language": "en-AE,en;q=0.9,ar;q=0.7",
                "Cache-Control": "no-cache",
                "Pragma": "no-cache",
                "Upgrade-Insecure-Requests": "1",
            }
        )

    def scrape(self, *, query: str | None = None, limit: int | None = None) -> Iterable[Product]:
        category = self._category_for_query(query)
        yield from self._crawl_category(category, limit=limit)

    def crawl(
        self,
        *,
        limit: int | None = None,
        limit_categories: int | None = None,
    ) -> Iterable[Product]:
        categories = AMAZON_EG_CATEGORIES[: limit_categories or None]
        seen: set[str] = set()
        emitted = 0

        for category in categories:
            for product in self._crawl_category(category, limit=None):
                if product.private_key in seen:
                    continue

                seen.add(product.private_key)
                yield product
                emitted += 1

                if limit and emitted >= limit:
                    return

    def _category_for_query(self, query: str | None) -> dict[str, Any]:
        query = clean_text(query)
        if not query:
            return AMAZON_EG_CATEGORIES[0]

        normalized = query.lower()
        for category in AMAZON_EG_CATEGORIES:
            if normalized == category["name"].lower() or normalized in [value.lower() for value in category["keywords"]]:
                return category

        return {"name": query, "keywords": [query], "department": None, "nodes": []}

    def _crawl_category(self, category: dict[str, Any], *, limit: int | None) -> Iterable[Product]:
        emitted = 0
        seen: set[str] = set()

        for seed_url in self._seed_urls(category):
            next_url: str | None = seed_url
            page = 0

            while next_url and page < self.max_pages_per_seed:
                page += 1
                try:
                    html = self._fetch_html(next_url)
                except RuntimeError as exc:
                    print(f"amazon_eg: skipped {category['name']} page {page}: {exc}", file=sys.stderr, flush=True)
                    break

                products = list(self._products_from_html(html, category))
                if not products:
                    if self._is_sorry_page(html):
                        print(
                            f"amazon_eg: skipped {category['name']} page {page}: Amazon returned a sorry page",
                            file=sys.stderr,
                            flush=True,
                        )
                    break

                for product in products:
                    if product.private_key in seen:
                        continue
                    seen.add(product.private_key)
                    yield product
                    emitted += 1

                    if limit and emitted >= limit:
                        return

                next_url = self._next_url(html)

    def _seed_urls(self, category: dict[str, Any]) -> list[str]:
        urls = []

        for node in category.get("nodes") or []:
            urls.append(f"{self.base_url}/-/en/s?rh=n%3A{node}%2C{quote_plus(FBA_REFINEMENT)}")

        for keyword in category.get("keywords") or []:
            keyword_query = quote_plus(keyword)
            department = category.get("department")
            if department:
                urls.append(
                    f"{self.base_url}/-/en/s?k={keyword_query}&i={quote_plus(department)}&rh={quote_plus(FBA_REFINEMENT)}"
                )
            urls.append(f"{self.base_url}/-/en/s?k={keyword_query}&rh={quote_plus(FBA_REFINEMENT)}")

        return list(dict.fromkeys(urls))

    def _fetch_html(self, url: str) -> str:
        result = self.client.get(url)
        if result.status_code >= 400:
            raise RuntimeError(f"HTTP {result.status_code}")
        return result.text

    def _products_from_html(self, html: str, category: dict[str, Any]) -> Iterable[Product]:
        if self._is_sorry_page(html):
            return

        soup = BeautifulSoup(html, "lxml")
        cards = soup.select('[data-component-type="s-search-result"][data-asin]')

        for card in cards:
            product = self._product_from_card(card, category)
            if product:
                yield product

    def _product_from_card(self, card: Any, category: dict[str, Any]) -> Product | None:
        asin = clean_text(card.get("data-asin"))
        title_node = card.select_one("h2 a span") or card.select_one("h2 span")
        title = clean_text(title_node.get_text(" ", strip=True) if title_node else "")
        image_node = card.select_one("img.s-image")
        image_url = image_node.get("src") if image_node else None
        price_node = card.select_one(".a-price .a-offscreen")
        price = parse_price(price_node.get_text(" ", strip=True) if price_node else None)
        link_node = card.select_one("h2 a[href]") or card.select_one('a[href*="/dp/"]')
        product_url = absolute_url(self.base_url, link_node.get("href") if link_node else None)
        card_text = clean_text(card.get_text(" ", strip=True))

        if not asin or not title or price is None or not image_url:
            return None
        if "currently unavailable" in card_text.lower() or "no featured offers available" in card_text.lower():
            return None
        if should_skip_cold_chain(title, category["name"], card_text):
            return None

        return Product(
            source=self.name,
            source_product_id=asin,
            source_sku=asin,
            name=title,
            price=price,
            currency="EGP",
            image_url=image_url,
            description=None,
            detail=None,
            product_url=product_url,
            category_path=["Amazon.eg", category["name"]],
            raw={
                "asin": asin,
                "source_category": category["name"],
                "fulfilled_by_amazon_filter": FBA_REFINEMENT,
            },
        )

    def _next_url(self, html: str) -> str | None:
        soup = BeautifulSoup(html, "lxml")
        next_link = soup.select_one("a.s-pagination-next[href]")
        if not next_link:
            return None
        if "s-pagination-disabled" in (next_link.get("class") or []):
            return None
        return absolute_url(self.base_url, next_link.get("href"))

    def _is_sorry_page(self, html: str) -> bool:
        lowered = html.lower()
        return "عذرًا" in html or "sorry" in lowered and "amazon" in lowered and "captcha" in lowered
