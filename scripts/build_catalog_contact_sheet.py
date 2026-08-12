from __future__ import annotations

import argparse
import json
import textwrap
from pathlib import Path

from PIL import Image, ImageDraw, ImageFont, ImageOps


def main() -> None:
    parser = argparse.ArgumentParser()
    parser.add_argument("products")
    parser.add_argument("output")
    parser.add_argument("--columns", type=int, default=4)
    args = parser.parse_args()

    rows = [json.loads(line) for line in Path(args.products).read_text(encoding="utf-8").splitlines() if line]
    tile_width, tile_height = 420, 390
    row_count = (len(rows) + args.columns - 1) // args.columns
    sheet = Image.new("RGB", (tile_width * args.columns, tile_height * row_count), "white")
    draw = ImageDraw.Draw(sheet)
    font = ImageFont.load_default(size=18)
    small = ImageFont.load_default(size=15)

    for index, row in enumerate(rows):
        x = (index % args.columns) * tile_width
        y = (index // args.columns) * tile_height
        with Image.open(row["local_image_path"]) as source:
            source = ImageOps.exif_transpose(source).convert("RGB")
            source.thumbnail((tile_width - 30, 265))
            image_x = x + (tile_width - source.width) // 2
            sheet.paste(source, (image_x, y + 10))
        draw.rectangle((x, y, x + tile_width - 1, y + tile_height - 1), outline="#bbbbbb")
        label = "\n".join(textwrap.wrap(row["name"], width=42)[:3])
        draw.text((x + 12, y + 280), label, fill="black", font=font)
        draw.text(
            (x + 12, y + 355),
            f"{row['source']} | {row['source_product_id']} | EGP {row['price']}",
            fill="#444444",
            font=small,
        )

    sheet.save(args.output, quality=92)


if __name__ == "__main__":
    main()
