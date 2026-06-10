from __future__ import annotations

from dataclasses import asdict, dataclass, field
from datetime import datetime, timezone
from typing import Any


@dataclass
class Product:
    source: str
    source_product_id: str | None
    source_sku: str | None
    name: str
    price: float | None
    currency: str = "EGP"
    image_url: str | None = None
    description: str | None = None
    detail: str | None = None
    product_url: str | None = None
    category_path: list[str] = field(default_factory=list)
    scraped_at: str = field(default_factory=lambda: datetime.now(timezone.utc).isoformat())
    raw: dict[str, Any] = field(default_factory=dict)

    def to_dict(self) -> dict[str, Any]:
        return asdict(self)

    @property
    def private_key(self) -> str:
        if self.source_product_id:
            return f"{self.source}:{self.source_product_id}"
        if self.source_sku:
            return f"{self.source}:sku:{self.source_sku}"
        return f"{self.source}:name:{self.name.lower()}"
