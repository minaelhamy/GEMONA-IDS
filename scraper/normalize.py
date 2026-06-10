from __future__ import annotations

import hashlib
import re
import unicodedata
from difflib import SequenceMatcher
from urllib.parse import urljoin

from .settings import COLD_CHAIN_KEYWORDS

PACKAGE_RE = re.compile(
    r"(?P<count>\d+\s*[xX]\s*)?(?P<size>\d+(?:[.,]\d+)?)\s*(?P<unit>kg|g|gm|ml|l|litre|liter|pcs|piece|pack|rolls?)\b",
    re.I,
)
GTIN_RE = re.compile(r"^\d{8,14}$")


def clean_text(value: str | None) -> str:
    if not value:
        return ""
    value = unicodedata.normalize("NFKC", value)
    return re.sub(r"\s+", " ", value).strip()


def parse_price(value: str | None) -> float | None:
    if not value:
        return None
    match = re.search(r"(\d+(?:[,.]\d+)?)", value.replace(",", ""))
    if not match:
        return None
    return float(match.group(1))


def absolute_url(base_url: str, value: str | None) -> str | None:
    if not value:
        return None
    return urljoin(base_url, value)


def should_skip_cold_chain(*values: str | None) -> bool:
    text = " ".join(clean_text(v).lower() for v in values if v)
    return any(keyword in text for keyword in COLD_CHAIN_KEYWORDS)


def normalize_name(value: str) -> str:
    value = clean_text(value).lower()
    value = re.sub(r"[^\w\s.,x/-]", " ", value)
    value = re.sub(r"\b(egp|offer|new|promo|sale|online|buy)\b", " ", value)
    return clean_text(value)


def package_signature(value: str) -> str | None:
    matches = []
    for match in PACKAGE_RE.finditer(value):
        count = re.sub(r"\s+", "", match.group("count") or "")
        size = match.group("size").replace(",", ".")
        unit = match.group("unit").lower()
        matches.append(f"{count}{size}{unit}")
    return "|".join(matches) or None


def blocking_key(name: str, sku: str | None = None, category_hint: str | None = None) -> str:
    if sku and GTIN_RE.match(sku):
        return f"gtin:{sku}"
    normalized = normalize_name(name)
    package = package_signature(normalized) or "no-size"
    tokens = [token for token in normalized.split() if len(token) > 2]
    stem = " ".join(tokens[:4])
    category = normalize_name(category_hint or "") if package == "no-size" else ""
    digest = hashlib.sha1(f"{stem}|{package}|{category}".encode("utf-8")).hexdigest()[:12]
    return f"name:{digest}"


def similarity(left: str, right: str) -> float:
    return SequenceMatcher(None, normalize_name(left), normalize_name(right)).ratio()
