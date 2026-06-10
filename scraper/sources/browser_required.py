from __future__ import annotations

from collections.abc import Iterable

from ..models import Product
from .base import Source


class BrowserRequiredSource(Source):
    base_url: str = ""
    reason: str = "This source requires a rendered browser session."

    def scrape(self, *, query: str | None = None, limit: int | None = None) -> Iterable[Product]:
        result = self.client.get(self.base_url)
        raise RuntimeError(f"{self.name} requires browser extraction. HTTP status={result.status_code}. {self.reason}")


class HyperOneSource(BrowserRequiredSource):
    name = "hyperone"
    base_url = "https://www.hyperone.com.eg/en"
    reason = "Plain HTTP returns 403; use browser context with Maadi or Sheikh Zayed location."


class CarrefourSource(BrowserRequiredSource):
    name = "carrefour"
    base_url = "https://www.carrefouregypt.com/mafegy/ar"
    reason = "MAF storefront requires session/location handling and likely bot protection."
