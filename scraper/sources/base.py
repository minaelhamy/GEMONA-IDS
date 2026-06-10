from __future__ import annotations

from abc import ABC, abstractmethod
from collections.abc import Iterable

from ..http import HttpClient
from ..models import Product


class Source(ABC):
    name: str

    def __init__(self, client: HttpClient | None = None) -> None:
        self.client = client or HttpClient()

    @abstractmethod
    def scrape(self, *, query: str | None = None, limit: int | None = None) -> Iterable[Product]:
        raise NotImplementedError

    def crawl(
        self,
        *,
        limit: int | None = None,
        limit_categories: int | None = None,
    ) -> Iterable[Product]:
        yield from self.scrape(query=None, limit=limit)
