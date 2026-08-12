from __future__ import annotations

import hashlib
import io
import re
from dataclasses import replace
from pathlib import Path
from urllib.parse import urlparse

from PIL import Image, ImageStat, UnidentifiedImageError

from .http import HttpClient
from .models import Product


MIN_IMAGE_SIDE = 200
MIN_IMAGE_BYTES = 4_096


class ImageValidationError(RuntimeError):
    pass


def stage_product_image(product: Product, root: Path, client: HttpClient | None = None) -> Product:
    if not product.image_url:
        raise ImageValidationError("missing image URL")

    client = client or HttpClient(delay_seconds=0.1)
    result = client.get(
        product.image_url,
        headers={"Accept": "image/avif,image/webp,image/png,image/jpeg,image/*;q=0.8"},
    )
    if result.status_code >= 400:
        raise ImageValidationError(f"image returned HTTP {result.status_code}")
    if len(result.content) < MIN_IMAGE_BYTES:
        raise ImageValidationError("image file is too small")

    try:
        with Image.open(io.BytesIO(result.content)) as image:
            image.load()
            width, height = image.size
            image_format = (image.format or "").lower()
            grayscale = image.convert("L").resize((64, 64))
            entropy = grayscale.entropy()
            standard_deviation = ImageStat.Stat(grayscale).stddev[0]
    except (UnidentifiedImageError, OSError, ValueError) as exc:
        raise ImageValidationError("download is not a decodable image") from exc

    if width < MIN_IMAGE_SIDE or height < MIN_IMAGE_SIDE:
        raise ImageValidationError(f"image dimensions are too small ({width}x{height})")
    if entropy < 1.0 or standard_deviation < 3.0:
        raise ImageValidationError("image appears blank or placeholder-like")

    digest = hashlib.sha256(result.content).hexdigest()
    extension = _extension(image_format, product.image_url)
    safe_id = re.sub(r"[^A-Za-z0-9._-]+", "-", product.source_product_id or product.source_sku or digest)
    relative = Path(product.source) / digest[:2] / f"{safe_id}-{digest[:12]}.{extension}"
    destination = root / relative
    destination.parent.mkdir(parents=True, exist_ok=True)
    destination.write_bytes(result.content)

    return replace(
        product,
        local_image_path=str(destination.resolve()),
        image_sha256=digest,
        image_width=width,
        image_height=height,
    )


def _extension(image_format: str, url: str) -> str:
    mapping = {"jpeg": "jpg", "png": "png", "webp": "webp", "gif": "gif", "avif": "avif"}
    if image_format in mapping:
        return mapping[image_format]
    suffix = Path(urlparse(url).path).suffix.lower().lstrip(".")
    return "jpg" if suffix not in {"jpg", "jpeg", "png", "webp", "gif", "avif"} else suffix
