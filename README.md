# Egypt Grocery Scraper

Local-first scraping system for collecting product data from Egyptian online supermarkets, normalizing the fields, excluding cold-chain/fresh products, and clustering likely duplicates before sending products to an ecommerce platform.

## What It Extracts

Each source adapter returns this private normalized record:

- `source`: internal source name, never shown to customers
- `source_product_id`: source-specific product ID
- `source_sku`: barcode/SKU when available
- `name`
- `price`
- `currency`
- `image_url`
- `description`
- `detail`
- `product_url`
- `category_path`
- `raw`

## Quick Start

```bash
python3 -m scraper.cli scrape --source gourmet --query water --limit 20
python3 -m scraper.cli scrape --source seoudi --query water --limit 30
python3 -m scraper.cli scrape --source mahmoud_elfar --query water --limit 30
python3 -m scraper.cli crawl --source hyperone --limit 300 --progress-every 100
python3 -m scraper.cli scrape-many --sources seoudi mahmoud_elfar --query water --limit-per-source 30
python3 -m scraper.cli merge data/runs/<seoudi>/products.jsonl data/runs/<elfar>/products.jsonl data/runs/<hyperone>/products.jsonl
python3 -m scraper.cli dedupe data/latest/products.jsonl
```

Outputs are written under `data/runs/<timestamp>/` and copied to `data/latest/`.

## Sources

- `gourmet`: implemented HTTP parser for Magento search/category/listing pages.
- `seoudi`: implemented through Magento GraphQL with full category discovery, pagination, retry, and per-category skip guards.
- `mahmoud_elfar`: implemented through the web API. It selects Maadi/Cairo for the location gate, discovers categories, then crawls category pages.
- `hyperone`: implemented through Magento GraphQL. It selects a Maadi/Sheikh Zayed-backed store source, discovers categories, and crawls products.
- `carrefour`: implemented through browser-grade TLS category HTML fetching. It discovers non-cold categories from Carrefour's all-categories page, walks `currentPage`, and parses embedded Next.js product-card data. The MAF `v8` API path remains as a fallback, and rendered HTML snapshots can also be imported manually.

Current verified import:

- Seoudi: 13,975 products
- Mahmoud El Far: 12,934 products
- HyperOne: 5,351 products
- Carrefour: 10,910 products
- Combined latest dataset: 43,170 products, 40,565 dedupe clusters

Carrefour fallback command for rendered HTML snapshots:

```bash
python3 -m scraper.cli import-carrefour-html saved-carrefour-category.html --category-path "Food Cupboard>Breakfast Cereals & Bars"
```

## Duplicate Strategy

The dedupe step clusters records by:

1. Exact barcode/SKU when it looks like a GTIN.
2. Normalized name plus extracted package size, for example `nestle water 12 x 600 ml`.
3. Fuzzy token similarity as a fallback, with a conservative threshold.

The source fields stay private, so the customer sees one merged product while you keep supplier candidates internally for offline sourcing decisions.

## Cold-Chain Exclusion

Adapters skip products/categories containing configured keywords such as fresh, chilled, frozen, meat, poultry, fish, dairy, eggs, cheese, yogurt, ice cream, fruit, and vegetables. Adjust `scraper/settings.py` when you decide to include or exclude more categories.
