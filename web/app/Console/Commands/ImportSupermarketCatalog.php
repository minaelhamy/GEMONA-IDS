<?php

namespace App\Console\Commands;

use App\Enums\Activity;
use App\Enums\Ask;
use App\Enums\ShippingType;
use App\Enums\Status;
use App\Models\Product;
use App\Models\ProductCategory;
use App\Models\SupermarketProductSource;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class ImportSupermarketCatalog extends Command
{
    protected $signature = 'supermarket:import
        {--products= : Path to products.jsonl}
        {--clusters= : Path to clusters.json}
        {--margin= : Margin percentage to add to source prices}
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

                $product = Product::withTrashed()->where('external_key', $externalKey)->first();
                $isNew = !$product;
                $product ??= new Product();

                if ($product->trashed()) {
                    $product->restore();
                }

                $categoryId = $this->categoryId($canonical['category_path'] ?? []);
                $description = $this->descriptionFor($canonical);
                $name = (string) ($cluster['canonical_name'] ?? $canonical['name']);

                $payload = [
                    'name' => $this->safeName($name),
                    'slug' => $this->stableSlug($name, $cluster['cluster_id']),
                    'sku' => 'GEM-' . $cluster['cluster_id'],
                    'product_category_id' => $categoryId,
                    'buying_price' => $basePrice,
                    'status' => Status::ACTIVE,
                    'can_purchasable' => Ask::YES,
                    'show_stock_out' => Activity::ENABLE,
                    'maximum_purchase_quantity' => 10,
                    'low_stock_quantity_warning' => 1,
                    'refundable' => Ask::YES,
                    'description' => $description,
                    'shipping_type' => ShippingType::FREE,
                    'shipping_cost' => 0,
                    'source_type' => 'supermarket',
                    'external_key' => $externalKey,
                    'external_image_url' => $canonical['image_url'] ?? null,
                    'supermarket_base_price' => $basePrice,
                    'supermarket_margin_percent' => $margin,
                    'supermarket_candidate_count' => count($candidateRows),
                    'supermarket_synced_at' => now(),
                ];

                if (!$product->manual_price_override) {
                    $payload['selling_price'] = $sellingPrice;
                    $payload['variation_price'] = $sellingPrice;
                }

                $product->fill($payload);
                $product->save();
                $isNew ? $created++ : $updated++;

                foreach ($candidateRows as $row) {
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
        $maxBaseLength = 255 - strlen($suffix);
        $base = Str::slug($name) ?: 'product';

        return Str::limit($base, $maxBaseLength, '') . $suffix;
    }

    private function safeName(string $name): string
    {
        return Str::limit(trim($name), 255, '');
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
