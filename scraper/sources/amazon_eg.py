from __future__ import annotations

import os
import time
from collections.abc import Iterable
from typing import Any

import requests

from ..models import Product
from ..normalize import clean_text, should_skip_cold_chain
from .base import Source


AMAZON_EG_CATEGORIES = [
    {
        "name": "Mobiles, Tablets & Accessories",
        "search_index": "Electronics",
        "keywords": "mobiles tablets accessories",
    },
    {
        "name": "Computers & Office Supplies",
        "search_index": "OfficeProducts",
        "keywords": "computers office supplies",
    },
    {
        "name": "TVs & Electronics",
        "search_index": "Electronics",
        "keywords": "tv electronics",
    },
    {
        "name": "Women's Fashion",
        "search_index": "Fashion",
        "keywords": "women fashion",
    },
    {
        "name": "Men's Fashion",
        "search_index": "Fashion",
        "keywords": "men fashion",
    },
    {
        "name": "Kids Fashion",
        "search_index": "Fashion",
        "keywords": "kids fashion",
    },
    {
        "name": "Health, Beauty & Perfumes",
        "search_index": "Beauty",
        "keywords": "health beauty perfumes",
    },
    {
        "name": "Supermarket",
        "search_index": "Grocery",
        "keywords": "supermarket grocery",
    },
    {
        "name": "Home, Furniture & Tools",
        "search_index": "HomeImprovement",
        "keywords": "home furniture tools",
    },
    {
        "name": "Kitchen & Appliances",
        "search_index": "Home",
        "keywords": "kitchen appliances",
    },
    {
        "name": "Toys, Games & Baby",
        "search_index": "Toys",
        "keywords": "toys games baby",
    },
    {
        "name": "Sports, Fitness & Outdoors",
        "search_index": "SportsAndOutdoors",
        "keywords": "sports fitness outdoors",
    },
]


class AmazonEgSource(Source):
    name = "amazon_eg"
    marketplace = "www.amazon.eg"
    api_url = "https://creatorsapi.amazon/catalog/v1/searchItems"

    resources = [
        "images.primary.large",
        "itemInfo.byLineInfo",
        "itemInfo.classifications",
        "itemInfo.features",
        "itemInfo.productInfo",
        "itemInfo.technicalInfo",
        "itemInfo.title",
        "offersV2.listings.availability",
        "offersV2.listings.condition",
        "offersV2.listings.isBuyBoxWinner",
        "offersV2.listings.merchantInfo",
        "offersV2.listings.price",
        "offersV2.listings.type",
        "parentASIN",
    ]

    def __init__(self, client: Any | None = None) -> None:
        super().__init__(client=client)
        self.session = requests.Session()
        self._access_token: str | None = None
        self._token_expires_at = 0.0

    def scrape(self, *, query: str | None = None, limit: int | None = None) -> Iterable[Product]:
        category = self._category_for_query(query)
        yield from self._search_category(category, limit=limit)

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
            for product in self._search_category(category, limit=None):
                if product.private_key in seen:
                    continue

                seen.add(product.private_key)
                yield product
                emitted += 1

                if limit and emitted >= limit:
                    return

    def _category_for_query(self, query: str | None) -> dict[str, str]:
        query = clean_text(query)
        if not query:
            return AMAZON_EG_CATEGORIES[0]

        normalized = query.lower()
        for category in AMAZON_EG_CATEGORIES:
            if normalized in {
                category["name"].lower(),
                category["search_index"].lower(),
                category["keywords"].lower(),
            }:
                return category

        return {"name": query, "search_index": "All", "keywords": query}

    def _search_category(self, category: dict[str, str], *, limit: int | None) -> Iterable[Product]:
        emitted = 0
        total = None

        for page in range(1, 11):
            payload = self._search_payload(category, page=page)
            data = self._post(payload)
            result = data.get("searchResult") or {}
            total = total if total is not None else int(result.get("totalResultCount") or 0)
            items = result.get("items") or []
            if not items:
                return

            for item in items:
                product = self._item_to_product(item, category)
                if not product:
                    continue

                yield product
                emitted += 1

                if limit and emitted >= limit:
                    return

            if emitted >= total:
                return

    def _search_payload(self, category: dict[str, str], *, page: int) -> dict[str, Any]:
        return {
            "marketplace": self.marketplace,
            "partnerTag": self._required_env("AMAZON_EG_PARTNER_TAG"),
            "keywords": category["keywords"],
            "searchIndex": category["search_index"],
            "itemCount": 10,
            "itemPage": page,
            "availability": "Available",
            "condition": "New",
            "currencyOfPreference": "EGP",
            "languagesOfPreference": ["en_AE"],
            "deliveryFlags": ["FulfilledByAmazon"],
            "resources": self.resources,
        }

    def _post(self, payload: dict[str, Any]) -> dict[str, Any]:
        token = self._token()
        credential_version = clean_text(os.getenv("AMAZON_CREATORS_CREDENTIAL_VERSION")) or "3.2"
        authorization = f"Bearer {token}"
        if credential_version.startswith("2."):
            authorization = f"{authorization}, Version {credential_version}"

        response = self.session.post(
            self.api_url,
            json=payload,
            headers={
                "Authorization": authorization,
                "Content-Type": "application/json",
                "x-marketplace": self.marketplace,
            },
            timeout=30,
        )
        if response.status_code >= 400:
            raise RuntimeError(f"Amazon Creators API failed with HTTP {response.status_code}: {response.text[:500]}")
        data = response.json()
        if data.get("errors"):
            raise RuntimeError(f"Amazon Creators API errors: {data['errors']}")
        return data

    def _token(self) -> str:
        now = time.time()
        if self._access_token and now < self._token_expires_at - 60:
            return self._access_token

        client_id = self._required_env("AMAZON_CREATORS_CLIENT_ID")
        client_secret = self._required_env("AMAZON_CREATORS_CLIENT_SECRET")
        credential_version = clean_text(os.getenv("AMAZON_CREATORS_CREDENTIAL_VERSION")) or "3.2"

        if credential_version.startswith("2."):
            token_url = (
                clean_text(os.getenv("AMAZON_CREATORS_TOKEN_URL"))
                or "https://creatorsapi.auth.eu-south-2.amazoncognito.com/oauth2/token"
            )
            response = self.session.post(
                token_url,
                data={
                    "grant_type": "client_credentials",
                    "client_id": client_id,
                    "client_secret": client_secret,
                    "scope": "creatorsapi/default",
                },
                headers={"Content-Type": "application/x-www-form-urlencoded"},
                timeout=30,
            )
        else:
            token_url = (
                clean_text(os.getenv("AMAZON_CREATORS_TOKEN_URL"))
                or "https://api.amazon.co.uk/auth/o2/token"
            )
            response = self.session.post(
                token_url,
                json={
                    "grant_type": "client_credentials",
                    "client_id": client_id,
                    "client_secret": client_secret,
                    "scope": "creatorsapi::default",
                },
                headers={"Content-Type": "application/json"},
                timeout=30,
            )

        if response.status_code >= 400:
            raise RuntimeError(f"Amazon Creators API token request failed with HTTP {response.status_code}: {response.text[:500]}")

        payload = response.json()
        self._access_token = payload["access_token"]
        self._token_expires_at = now + int(payload.get("expires_in") or 3600)
        return self._access_token

    def _item_to_product(self, item: dict[str, Any], category: dict[str, str]) -> Product | None:
        asin = clean_text(item.get("asin"))
        item_info = item.get("itemInfo") or {}
        title = clean_text(((item_info.get("title") or {}).get("displayValue")))
        listings = (item.get("offersV2") or {}).get("listings") or []
        if isinstance(listings, dict):
            listings = [listings]
        listing = self._best_listing(listings)
        price = (((listing.get("price") or {}).get("money") or {}).get("amount"))
        image_url = ((((item.get("images") or {}).get("primary") or {}).get("large") or {}).get("url"))
        features = ((item_info.get("features") or {}).get("displayValues") or [])

        if not asin or not title or not price or not image_url:
            return None
        if should_skip_cold_chain(title, category["name"], " ".join(str(feature) for feature in features)):
            return None

        brand = clean_text(((item_info.get("byLineInfo") or {}).get("brand") or {}).get("displayValue"))
        availability = (listing.get("availability") or {}).get("type")

        return Product(
            source=self.name,
            source_product_id=asin,
            source_sku=asin,
            name=title,
            price=float(price),
            currency=(((listing.get("price") or {}).get("money") or {}).get("currency")) or "EGP",
            image_url=image_url,
            description=brand or None,
            detail="\n".join(clean_text(str(feature)) for feature in features if clean_text(str(feature))) or None,
            product_url=item.get("detailPageURL"),
            category_path=["Amazon.eg", category["name"]],
            raw={
                "asin": asin,
                "parent_asin": item.get("parentASIN"),
                "merchant": (listing.get("merchantInfo") or {}).get("name"),
                "availability": availability,
                "delivery_flags": ["FulfilledByAmazon"],
                "search_index": category["search_index"],
                "source_category": category["name"],
            },
        )

    def _best_listing(self, listings: list[dict[str, Any]]) -> dict[str, Any]:
        if not listings:
            return {}
        buy_box = [listing for listing in listings if listing.get("isBuyBoxWinner")]
        return (buy_box or listings)[0]

    def _required_env(self, name: str) -> str:
        value = clean_text(os.getenv(name))
        if not value:
            raise RuntimeError(
                f"{name} is required for amazon_eg. Add Amazon Creators API credentials to .env "
                "before enabling the amazon_eg source."
            )
        return value
