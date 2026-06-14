<?php

namespace App\Console\Commands;

use App\Enums\Activity;
use App\Enums\Ask;
use App\Enums\ShippingType;
use App\Enums\Status;
use App\Models\Product;
use App\Models\ProductAttribute;
use App\Models\ProductAttributeOption;
use App\Models\ProductBrand;
use App\Models\ProductVariation;
use App\Models\ProductCategory;
use App\Models\SupermarketProductSource;
use App\Models\Unit;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class ImportSupermarketCatalog extends Command
{
    private const PRODUCT_TEXT_LIMIT = 190;

    protected $signature = 'supermarket:import
        {--products= : Path to products.jsonl}
        {--clusters= : Path to clusters.json}
        {--margin= : Margin percentage to add to source prices}
        {--prices-only : For existing supermarket products, update only price, availability, source candidates, and sync metadata}
        {--dry-run : Read and validate the data without writing}';

    protected $description = 'Import deduplicated supermarket catalog products into GEMONA IDS.';

    public function handle(): int
    {
        ini_set('memory_limit', (string) config('supermarkets.memory_limit', '512M'));

        $productsPath = $this->resolvePath($this->option('products') ?: config('supermarkets.products_path'));
        $clustersPath = $this->resolvePath($this->option('clusters') ?: config('supermarkets.clusters_path'));
        $margin = (float) ($this->option('margin') ?? config('supermarkets.margin_percent', 10));

        if (!is_file($productsPath)) {
            $this->error("Products file not found: {$productsPath}");
            return self::FAILURE;
        }
        if (!is_file($clustersPath)) {
            $this->error("Clusters file not found: {$clustersPath}");
            return self::FAILURE;
        }

        $sourceProducts = $this->loadSourceProducts($productsPath);
        $clusters = json_decode(file_get_contents($clustersPath), true);

        if (!is_array($clusters)) {
            $this->error("Clusters file is not valid JSON: {$clustersPath}");
            return self::FAILURE;
        }

        $this->info(sprintf(
            'Importing %s clusters from %s source rows with %.2f%% margin.',
            number_format(count($clusters)),
            number_format(count($sourceProducts)),
            $margin
        ));

        if ($this->option('dry-run')) {
            return self::SUCCESS;
        }

        $created = 0;
        $updated = 0;
        $sources = 0;
        $skipped = 0;

        foreach ($clusters as $index => $cluster) {
            $candidateRows = $this->candidateRows($cluster, $sourceProducts);
            $canonical = $this->canonicalProduct($cluster, $candidateRows);

            if (!$canonical) {
                $skipped++;
                continue;
            }

            DB::transaction(function () use (
                $cluster,
                $candidateRows,
                $canonical,
                $margin,
                &$created,
                &$updated,
                &$sources
            ) {
                $externalKey = 'supermarket:' . $cluster['cluster_id'];
                $basePrice = (float) ($cluster['min_price'] ?? $canonical['price'] ?? 0);
                $sellingPrice = round($basePrice * (1 + ($margin / 100)), 2);
                $availability = $this->availabilityFor($candidateRows);

                $product = Product::withTrashed()->where('external_key', $externalKey)->first();
                $isNew = !$product;
                $product ??= new Product();

                if ($product->trashed()) {
                    $product->restore();
                }

                $categoryId = $this->categoryId($canonical['category_path'] ?? []);
                $brandId = $this->brandId($canonical, (string) ($cluster['canonical_name'] ?? $canonical['name']));
                $description = $this->descriptionFor($canonical);
                $name = (string) ($cluster['canonical_name'] ?? $canonical['name']);

                $syncPayload = [
                    'buying_price' => $basePrice,
                    'source_type' => 'supermarket',
                    'external_key' => $externalKey,
                    'supermarket_base_price' => $basePrice,
                    'supermarket_margin_percent' => $margin,
                    'supermarket_candidate_count' => count($candidateRows),
                    'supermarket_available' => $availability['available'],
                    'supermarket_available_quantity' => $availability['quantity'],
                    'supermarket_synced_at' => now(),
                ];

                $catalogPayload = [
                    'name' => $this->safeName($name),
                    'slug' => $this->stableSlug($name, $cluster['cluster_id']),
                    'sku' => 'GEM-' . $cluster['cluster_id'],
                    'product_category_id' => $categoryId,
                    'product_brand_id' => $brandId,
                    'unit_id' => $this->unitId($name),
                    'status' => Status::ACTIVE,
                    'can_purchasable' => Ask::YES,
                    'show_stock_out' => Activity::ENABLE,
                    'maximum_purchase_quantity' => 10,
                    'low_stock_quantity_warning' => 1,
                    'refundable' => Ask::YES,
                    'description' => $description,
                    'shipping_type' => ShippingType::FREE,
                    'shipping_cost' => 0,
                    'external_image_url' => $canonical['image_url'] ?? null,
                ];

                $payload = (!$isNew && $this->option('prices-only'))
                    ? $syncPayload
                    : array_merge($catalogPayload, $syncPayload);

                if (!$product->manual_price_override) {
                    $payload['selling_price'] = $sellingPrice;
                    $payload['variation_price'] = $sellingPrice;
                }

                $product->fill($payload);
                $product->save();
                $this->syncPackageVariation($product, $name, $sellingPrice);
                $isNew ? $created++ : $updated++;

                foreach ($candidateRows as $row) {
                    $sourceAvailability = $this->availabilityFor([$row]);
                    SupermarketProductSource::updateOrCreate(
                        [
                            'source' => (string) $row['source'],
                            'source_product_id' => (string) $row['source_product_id'],
                        ],
                        [
                            'product_id' => $product->id,
                            'source_sku' => $row['source_sku'] ?? null,
                            'source_price' => $row['price'] ?? null,
                            'source_currency' => $row['currency'] ?? 'EGP',
                            'source_image_url' => $row['image_url'] ?? null,
                            'source_product_url' => $row['product_url'] ?? null,
                            'source_category_path' => $row['category_path'] ?? null,
                            'source_available' => $sourceAvailability['available'],
                            'source_available_quantity' => $sourceAvailability['quantity'],
                            'source_payload' => $row,
                            'scraped_at' => $this->parseDate($row['scraped_at'] ?? null),
                        ]
                    );
                    $sources++;
                }
            });

            if (($index + 1) % 1000 === 0) {
                $this->line(sprintf('%s clusters processed...', number_format($index + 1)));
            }
        }

        $this->info(sprintf(
            'Done. Created: %s, updated: %s, source candidates: %s, skipped: %s.',
            number_format($created),
            number_format($updated),
            number_format($sources),
            number_format($skipped)
        ));

        return self::SUCCESS;
    }

    private function loadSourceProducts(string $path): array
    {
        $products = [];
        $handle = fopen($path, 'rb');

        while (($line = fgets($handle)) !== false) {
            $row = json_decode($line, true);
            if (!is_array($row) || empty($row['source']) || empty($row['source_product_id'])) {
                continue;
            }
            $products[$this->sourceKey($row['source'], $row['source_product_id'])] = $row;
        }

        fclose($handle);

        return $products;
    }

    private function candidateRows(array $cluster, array $sourceProducts): array
    {
        $rows = [];
        foreach ($cluster['candidate_sources'] ?? [] as $candidate) {
            $key = $this->sourceKey($candidate['source'] ?? '', $candidate['source_product_id'] ?? '');
            $rows[] = array_replace($candidate, $sourceProducts[$key] ?? []);
        }

        return array_values(array_filter($rows, fn ($row) => !empty($row['source']) && !empty($row['source_product_id'])));
    }

    private function canonicalProduct(array $cluster, array $candidateRows): ?array
    {
        if (!$candidateRows) {
            return null;
        }

        usort($candidateRows, fn ($a, $b) => ((float) ($a['price'] ?? PHP_FLOAT_MAX)) <=> ((float) ($b['price'] ?? PHP_FLOAT_MAX)));
        $canonical = $candidateRows[0];
        $canonical['name'] = $cluster['canonical_name'] ?? $canonical['name'] ?? null;

        return empty($canonical['name']) ? null : $canonical;
    }

    private function categoryId(array $path): ?int
    {
        $path = array_values(array_filter(array_map('trim', $path)));
        if (!$path) {
            $path = ['Grocery'];
        }

        $parentId = null;
        $slugParts = [];
        $category = null;

        foreach ($path as $name) {
            $slugParts[] = Str::slug($name) ?: 'category';
            $slug = implode('-', $slugParts);
            $category = ProductCategory::firstOrCreate(
                ['slug' => $slug],
                [
                    'name' => $name,
                    'description' => null,
                    'status' => Status::ACTIVE,
                    'parent_id' => $parentId,
                ]
            );
            if ($category->parent_id !== $parentId) {
                $category->parent_id = $parentId;
                $category->save();
            }
            $parentId = $category->id;
        }

        return $category?->id;
    }

    private function brandId(array $row, string $name): ?int
    {
        $brand = Arr::get($row, 'brand')
            ?: Arr::get($row, 'manufacturer')
            ?: Arr::get($row, 'raw.brand')
            ?: Arr::get($row, 'raw.manufacturer')
            ?: $this->inferBrand($name);

        $brand = $this->cleanText((string) $brand);
        if (!$brand) {
            return null;
        }

        $slug = Str::slug($brand) ?: Str::slug(Str::limit($brand, 40, ''));
        if (!$slug) {
            return null;
        }

        return ProductBrand::firstOrCreate(
            ['slug' => $slug],
            [
                'name' => Str::limit($brand, self::PRODUCT_TEXT_LIMIT, ''),
                'description' => null,
                'status' => Status::ACTIVE,
            ]
        )->id;
    }

    private function inferBrand(string $name): ?string
    {
        $normalized = trim(preg_replace('/\s+/', ' ', $name));
        if ($normalized === '') {
            return null;
        }

        $knownPrefixes = [
            'Abu Auf',
            'Abu Shakra',
            'Al Doha',
            'Al Marai',
            'Al Shark',
            'Ariel',
            'Betty Crocker',
            'Coca Cola',
            'Dairy Queen',
            'Dove',
            'El Bawadi',
            'El Rashidi',
            'Fine Baby',
            'Head & Shoulders',
            'Heinz',
            'Johnson\'s',
            'La Vache',
            'Lipton',
            'L\'Oreal',
            'Nescafe',
            'Nestle',
            'Pampers',
            'Red Bull',
            'Seoudi',
        ];

        foreach ($knownPrefixes as $prefix) {
            if (Str::startsWith(Str::lower($normalized), Str::lower($prefix . ' '))) {
                return $prefix;
            }
        }

        $tokens = preg_split('/\s+/', $normalized);
        $first = trim($tokens[0] ?? '', " \t\n\r\0\x0B,.-:;()[]{}");
        if ($first === '') {
            return null;
        }

        if (in_array(Str::lower($first), ['el', 'al', 'abu', 'la'], true) && isset($tokens[1])) {
            return $first . ' ' . trim($tokens[1], " \t\n\r\0\x0B,.-:;()[]{}");
        }

        return $first;
    }

    private function syncPackageVariation(Product $product, string $name, float $sellingPrice): void
    {
        $packageSize = $this->packageSize($name);
        if (!$packageSize) {
            return;
        }

        $attribute = ProductAttribute::firstOrCreate(['name' => 'Package Size']);
        $option = ProductAttributeOption::firstOrCreate(
            [
                'product_attribute_id' => $attribute->id,
                'name' => $packageSize,
            ]
        );

        $variation = ProductVariation::firstOrNew([
            'product_id' => $product->id,
            'product_attribute_id' => $attribute->id,
            'product_attribute_option_id' => $option->id,
            'parent_id' => null,
        ]);

        $variation->fill([
            'price' => $product->manual_price_override ? $product->variation_price : $sellingPrice,
            'sku' => $product->sku,
            'order' => 1,
        ]);
        $variation->save();
    }

    private function unitId(string $name): ?int
    {
        $packageSize = $this->packageSize($name);
        if (!$packageSize) {
            return $this->unitByCode('pc', 'Piece');
        }

        $normalized = Str::lower($packageSize);
        $map = [
            'kg' => ['Kilogram', 'kg'],
            'g' => ['Gram', 'gm'],
            'gm' => ['Gram', 'gm'],
            'gram' => ['Gram', 'gm'],
            'grams' => ['Gram', 'gm'],
            'l' => ['Litre', 'lt'],
            'ltr' => ['Litre', 'lt'],
            'liter' => ['Litre', 'lt'],
            'litre' => ['Litre', 'lt'],
            'ml' => ['Milliliter', 'ml'],
            'pcs' => ['Piece', 'pc'],
            'pc' => ['Piece', 'pc'],
            'pieces' => ['Piece', 'pc'],
            'piece' => ['Piece', 'pc'],
            'pack' => ['Pack', 'pack'],
            'packs' => ['Pack', 'pack'],
            'rolls' => ['Roll', 'roll'],
            'sheets' => ['Sheet', 'sheet'],
            'wipes' => ['Wipe', 'wipe'],
            'bags' => ['Bag', 'bag'],
            'sachets' => ['Sachet', 'sachet'],
            'tabs' => ['Tablet', 'tab'],
            'tablets' => ['Tablet', 'tab'],
            'capsules' => ['Capsule', 'cap'],
        ];

        foreach ($map as $token => [$unitName, $code]) {
            if (preg_match('/\b' . preg_quote($token, '/') . '\b/i', $normalized)) {
                return $this->unitByCode($code, $unitName);
            }
        }

        return $this->unitByCode('pc', 'Piece');
    }

    private function unitByCode(string $code, string $name): ?int
    {
        return Unit::firstOrCreate(
            ['code' => $code],
            ['name' => $name, 'status' => Status::ACTIVE]
        )->id;
    }

    private function packageSize(string $name): ?string
    {
        $pattern = '/(?:^|[\s,\-])((?:\d+(?:[\.,]\d+)?\s*(?:kg|g|gm|gram|grams|l|ltr|liter|litre|ml|m|cm|mm|pcs|pc|pieces|piece|tabs|tablets|capsules|rolls|sheets|wipes|bags|sachets|pack|packs))|(?:\d+\s*[xX]\s*\d+(?:[\.,]\d+)?\s*(?:g|kg|ml|l|pcs|pieces|rolls|sheets)))\b/i';
        if (!preg_match($pattern, $name, $matches)) {
            return null;
        }

        return $this->cleanText(str_replace(',', '.', $matches[1]));
    }

    private function availabilityFor(array $rows): array
    {
        $available = false;
        $quantity = 0;
        $sawAvailability = false;

        foreach ($rows as $row) {
            $raw = Arr::get($row, 'raw', []);
            $raw = is_array($raw) ? $raw : [];
            $rowAvailable = null;
            $rowQuantity = null;

            if (array_key_exists('in_stock', $raw)) {
                $rowAvailable = (bool) $raw['in_stock'];
            } elseif (array_key_exists('stock_status', $raw)) {
                $rowAvailable = Str::upper((string) $raw['stock_status']) !== 'OUT_OF_STOCK';
            } elseif (array_key_exists('stock', $raw) && is_array($raw['stock'])) {
                $status = Str::lower((string) ($raw['stock']['stockLevelStatus'] ?? ''));
                $rowAvailable = $status !== '' ? $status !== 'outofstock' : null;
                $rowQuantity = isset($raw['stock']['value']) ? (int) $raw['stock']['value'] : null;
            }

            if (array_key_exists('available_quantity', $raw)) {
                $rowQuantity = (int) $raw['available_quantity'];
                $rowAvailable ??= $rowQuantity > 0;
            }

            if ($rowAvailable !== null) {
                $sawAvailability = true;
                $available = $available || $rowAvailable;
            }
            if ($rowQuantity !== null) {
                $quantity += max(0, $rowQuantity);
            }
        }

        return [
            'available' => $sawAvailability ? $available : true,
            'quantity' => $quantity > 0 ? $quantity : null,
        ];
    }

    private function descriptionFor(array $row): ?string
    {
        $parts = array_filter([
            Arr::get($row, 'description'),
            Arr::get($row, 'detail'),
        ]);

        return $parts ? implode("\n\n", array_unique($parts)) : null;
    }

    private function stableSlug(string $name, string $clusterId): string
    {
        $suffix = '-' . strtolower($clusterId);
        $maxBaseLength = self::PRODUCT_TEXT_LIMIT - strlen($suffix);
        $base = Str::slug($name) ?: 'product';

        return Str::limit($base, $maxBaseLength, '') . $suffix;
    }

    private function safeName(string $name): string
    {
        return Str::limit(trim($name), self::PRODUCT_TEXT_LIMIT, '');
    }

    private function cleanText(string $value): string
    {
        return trim(preg_replace('/\s+/', ' ', $value));
    }

    private function parseDate(?string $value): ?Carbon
    {
        if (!$value) {
            return null;
        }

        try {
            return Carbon::parse($value);
        } catch (\Throwable) {
            return null;
        }
    }

    private function sourceKey(string $source, string $id): string
    {
        return $source . '|' . $id;
    }

    private function resolvePath(string $path): string
    {
        if (Str::startsWith($path, ['/'])) {
            return $path;
        }

        return base_path($path);
    }
}
