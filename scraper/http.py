from __future__ import annotations

import time
from dataclasses import dataclass
from typing import Any

import requests

from .settings import REQUEST_TIMEOUT_SECONDS, USER_AGENT


@dataclass
class FetchResult:
    url: str
    status_code: int
    text: str
    headers: dict[str, str]


class HttpClient:
    def __init__(self, delay_seconds: float = 0.8) -> None:
        self.session = requests.Session()
        self.delay_seconds = delay_seconds
        self.session.headers.update(
            {
                "User-Agent": USER_AGENT,
                "Accept": "text/html,application/xhtml+xml,application/xml;q=0.9,application/json;q=0.8,*/*;q=0.7",
                "Accept-Language": "en-US,en;q=0.9,ar;q=0.8",
            }
        )

    def get(self, url: str, **kwargs: Any) -> FetchResult:
        time.sleep(self.delay_seconds)
        response = self.session.get(url, timeout=REQUEST_TIMEOUT_SECONDS, **kwargs)
        return FetchResult(
            url=response.url,
            status_code=response.status_code,
            text=response.text,
            headers=dict(response.headers),
        )

    def post_json(self, url: str, payload: dict[str, Any], **kwargs: Any) -> FetchResult:
        time.sleep(self.delay_seconds)
        headers = {
            "Accept": "application/json",
            "Content-Type": "application/json",
            **kwargs.pop("headers", {}),
        }
        response = self.session.post(
            url,
            json=payload,
            headers=headers,
            timeout=REQUEST_TIMEOUT_SECONDS,
            **kwargs,
        )
        return FetchResult(
            url=response.url,
            status_code=response.status_code,
            text=response.text,
            headers=dict(response.headers),
        )
