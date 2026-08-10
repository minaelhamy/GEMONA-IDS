<?php

namespace App\Console\Commands;

use App\Enums\Activity;
use App\Enums\Ask;
use App\Enums\Status;
use App\Models\Product;
use App\Models\ProductCategory;
use App\Models\ProductVariation;
use App\Models\SupermarketProductSource;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class RepairSupermarketCatalog extends Command
{
    private const CATEGORY_RULES = [
        'Beverages' => [
            'beverage', 'drink', 'water', 'juice', 'soda', 'cola', 'pepsi', 'coca', 'fanta', 'sprite',
            'tea', 'coffee', 'nescafe', 'milkshake', 'nectar', 'malt', 'energy drink',
        ],
        'Pantry Staples' => [
            'rice', 'pasta', 'flour', 'sugar', 'oil', 'vinegar', 'sauce', 'sauces', 'spice', 'spices',
            'seasoning', 'canned', 'tin', 'jar', 'honey', 'jam', 'spread', 'baking', 'pulses',
            'beans', 'lentil', 'noodle', 'soup', 'stock', 'condiment', 'dressing', 'marinade',
        ],
        'Snacks & Sweets' => [
            'snack', 'chips', 'crisps', 'chocolate', 'candy', 'sweet', 'confectionery', 'biscuit',
            'cookie', 'cracker', 'cake', 'wafer', 'popcorn', 'nuts', 'dates', 'dried fruit',
        ],
        'Breakfast & Bakery' => [
            'breakfast', 'cereal', 'oat', 'granola', 'corn flakes', 'bread', 'toast', 'bun', 'bakery',
            'croissant', 'pastry', 'baguette',
        ],
        'Cleaning & Household' => [
            'cleaning', 'detergent', 'laundry', 'dishwash', 'dish wash', 'bleach', 'disinfectant',
            'tissue', 'napkin', 'paper towel', 'toilet roll', 'garbage', 'trash', 'air freshener',
            'insect', 'pest', 'foil', 'cling film', 'food storage',
        ],
        'Personal Care' => [
            'personal care', 'shampoo', 'conditioner', 'hair', 'soap', 'shower', 'body wash', 'deodorant',
            'toothpaste', 'toothbrush', 'oral', 'skin', 'moisturizer', 'cream', 'lotion', 'razor',
            'feminine', 'sanitary', 'pads',
        ],
        'Baby Care' => [
            'baby', 'diaper', 'diapers', 'wipes', 'infant', 'kids care',
        ],
        'Health & Wellness' => [
            'health', 'vitamin', 'supplement', 'protein', 'medical', 'wellness', 'pharmacy',
        ],
        'Beauty & Cosmetics' => [
            'beauty', 'cosmetic', 'makeup', 'perfume', 'fragrance', 'lipstick', 'mascara',
        ],
        'Home & Kitchen' => [
            'kitchen', 'dining', 'cookware', 'tableware', 'glassware', 'storage', 'mop', 'broom',
            'home', 'household goods', 'disposable',
        ],
        'Appliances & Electronics' => [
            'appliance', 'electronics', 'oven', 'hob', 'hood', 'kettle', 'blender', 'mixer', 'fan',
            'heater', 'iron', 'vacuum', 'tv', 'television',
        ],
        'Pet Supplies' => [
            'pet', 'cat food', 'dog food', 'litter',
        ],
        'Other' => [],
    ];

    protected $signature = 'gemona:repair-supermarket-catalog
        {--margin=15 : Margin percentage to apply to supermarket prices}
        {--dry-run : Show what would change without writing to the database}
        {--keep-missing-images : Keep supermarket products active even when they do not have local product media}';

    protected $description = 'Normalize imported supermarket products: margin, duplicates, generated variations, categories, image visibility, and orderability.';

    public function handle(): int
    {
        $dryRun = (bool) $this->option('dry-run');
        $margin = (float) $this->option('margin');

        $this->info(sprintf(
            'Repairing supermarket catalog with %.2f%% margin%s.',
            $margin,
            $dryRun ? ' (dry run)' : ''
        ));

        $summary = [
            'orderable_products' => $this->repairOrderability($dryRun),
            'prices_updated' => $this->repairPrices($margin, $dryRun),
            'variations_removed' => $this->removeGeneratedVariations($dryRun),
            'duplicates_removed' => $this->removeDuplicates($margin, $dryRun),
            'categories_remapped' => $this->remapCategories($dryRun),
            'external_images_filled' => $this->fillExternalImageUrls($dryRun),
            'missing_local_images_hidden' => $this->option('keep-missing-images') ? 0 : $this->hideProductsMissingLocalImages($dryRun),
        ];

        foreach ($summary as $label => $count) {
            $this->line(sprintf('%s: %s', Str::headline($label), number_format($count)));
        }

        $this->printCoverage();

        return self::SUCCESS;
    }

    private function repairOrderability(bool $dryRun): int
    {
        $query = Product::query()->where('source_type', 'supermarket');
        $count = (clone $query)->count();

        if (!$dryRun) {
            $query->update([
                'can_purchasable' => Ask::YES,
                'show_stock_out' => Activity::DISABLE,
                'maximum_purchase_quantity' => Product::SUPERMARKET_ORDERABLE_QUANTITY,
            ]);
        }

        return $count;
    }

    private function repairPrices(float $margin, bool $dryRun): int
    {
        $sourcePrices = DB::table('supermarket_product_sources')
            ->select('product_id', DB::raw('MIN(source_price) as min_source_price'))
            ->where('source_available', true)
            ->whereNotNull('source_price')
            ->where('source_price', '>', 0)
            ->groupBy('product_id');

        $multiplier = 1 + ($margin / 100);

        $query = DB::table('products')
            ->joinSub($sourcePrices, 'source_prices', function ($join) {
                $join->on('source_prices.product_id', '=', 'products.id');
            })
            ->where('products.source_type', 'supermarket')
            ->where('products.manual_price_override', false)
            ->where(function ($query) use ($margin, $multiplier) {
                $query
                    ->whereRaw('ABS(products.supermarket_base_price - source_prices.min_source_price) > 0.01')
                    ->orWhereRaw('ABS(products.selling_price - ROUND(source_prices.min_source_price * ?, 2)) > 0.01', [$multiplier])
                    ->orWhereRaw('ABS(products.variation_price - ROUND(source_prices.min_source_price * ?, 2)) > 0.01', [$multiplier])
                    ->orWhereRaw('ABS(products.supermarket_margin_percent - ?) > 0.01', [$margin]);
            });

        $count = (clone $query)->count();

        if (!$dryRun) {
            $query->update([
                'buying_price' => DB::raw('source_prices.min_source_price'),
                'supermarket_base_price' => DB::raw('source_prices.min_source_price'),
                'supermarket_margin_percent' => $margin,
                'selling_price' => DB::raw('ROUND(source_prices.min_source_price * ' . $multiplier . ', 2)'),
                'variation_price' => DB::raw('ROUND(source_prices.min_source_price * ' . $multiplier . ', 2)'),
                'updated_at' => now(),
            ]);
        }

        return $count;
    }

    private function removeGeneratedVariations(bool $dryRun): int
    {
        $ids = ProductVariation::query()
            ->whereHas('product', fn ($query) => $query->where('source_type', 'supermarket'))
            ->whereDoesntHave('stocks')
            ->pluck('id');

        if (!$dryRun && $ids->isNotEmpty()) {
            ProductVariation::query()->whereIn('id', $ids)->delete();
            Product::query()
                ->where('source_type', 'supermarket')
                ->where('manual_price_override', false)
                ->update(['variation_price' => DB::raw('selling_price')]);
        }

        return $ids->count();
    }

    private function removeDuplicates(float $margin, bool $dryRun): int
    {
        $products = Product::query()
            ->withTrashed()
            ->where('source_type', 'supermarket')
            ->whereNull('deleted_at')
            ->withCount(['media as local_image_count' => fn ($query) => $query->where('collection_name', 'product')])
            ->get(['id', 'name', 'product_brand_id', 'supermarket_candidate_count', 'supermarket_base_price', 'selling_price', 'manual_price_override', 'created_at']);

        $groups = $products->groupBy(fn (Product $product) => $this->duplicateKey($product));
        $removed = 0;

        foreach ($groups as $key => $group) {
            if ($key === '' || $group->count() < 2) {
                continue;
            }

            $keeper = $group
                ->sortByDesc('local_image_count')
                ->sortByDesc('supermarket_candidate_count')
                ->sortBy('supermarket_base_price')
                ->first();

            $duplicates = $group->where('id', '!=', $keeper->id);
            if ($duplicates->isEmpty()) {
                continue;
            }

            $removed += $duplicates->count();

            if ($dryRun) {
                continue;
            }

            DB::transaction(function () use ($keeper, $duplicates, $margin) {
                $duplicateIds = $duplicates->pluck('id')->all();

                SupermarketProductSource::query()
                    ->whereIn('product_id', $duplicateIds)
                    ->update(['product_id' => $keeper->id]);

                ProductVariation::query()
                    ->whereIn('product_id', $duplicateIds)
                    ->whereDoesntHave('stocks')
                    ->delete();

                Product::query()
                    ->whereIn('id', $duplicateIds)
                    ->delete();

                $this->refreshProductFromSources($keeper->id, $margin);
            });
        }

        return $removed;
    }

    private function fillExternalImageUrls(bool $dryRun): int
    {
        $filled = 0;

        Product::query()
            ->where('source_type', 'supermarket')
            ->where(function ($query) {
                $query->whereNull('external_image_url')->orWhere('external_image_url', '');
            })
            ->select(['id'])
            ->chunkById(500, function ($products) use (&$filled, $dryRun) {
                foreach ($products as $product) {
                    $imageUrl = SupermarketProductSource::query()
                        ->where('product_id', $product->id)
                        ->whereNotNull('source_image_url')
                        ->where('source_image_url', '!=', '')
                        ->orderBy('source_available', 'desc')
                        ->orderBy('source_price')
                        ->value('source_image_url');

                    if (!$imageUrl) {
                        continue;
                    }

                    $filled++;
                    if (!$dryRun) {
                        Product::query()->whereKey($product->id)->update(['external_image_url' => $imageUrl]);
                    }
                }
            });

        return $filled;
    }

    private function remapCategories(bool $dryRun): int
    {
        $categoryIds = [];
        foreach (array_keys(self::CATEGORY_RULES) as $name) {
            $slug = 'gemona-' . Str::slug($name);

            if ($dryRun) {
                $categoryIds[$name] = ProductCategory::query()->where('slug', $slug)->value('id') ?? -1;
                continue;
            }

            $category = ProductCategory::query()->firstOrCreate(
                ['slug' => $slug],
                [
                    'name' => $name,
                    'description' => null,
                    'status' => Status::ACTIVE,
                    'parent_id' => null,
                ]
            );

            if ($category->name !== $name || $category->status !== Status::ACTIVE || $category->parent_id !== null) {
                $category->update([
                    'name' => $name,
                    'status' => Status::ACTIVE,
                    'parent_id' => null,
                ]);
            }

            $categoryIds[$name] = $category->id;
        }

        $changed = 0;
        Product::query()
            ->where('source_type', 'supermarket')
            ->select(['id', 'name', 'product_category_id'])
            ->chunkById(500, function ($products) use (&$changed, $categoryIds, $dryRun) {
                foreach ($products as $product) {
                    $categoryName = $this->categoryNameFor($product);
                    $categoryId = $categoryIds[$categoryName] ?? $categoryIds['Other'];

                    if ((int) $product->product_category_id === (int) $categoryId) {
                        continue;
                    }

                    $changed++;
                    if (!$dryRun) {
                        Product::query()->whereKey($product->id)->update(['product_category_id' => $categoryId]);
                    }
                }
            });

        if (!$dryRun) {
            ProductCategory::query()
                ->whereNotIn('id', array_values($categoryIds))
                ->whereDoesntHave('products', fn ($query) => $query->where('source_type', '!=', 'supermarket'))
                ->whereDoesntHave('products')
                ->update(['status' => Status::INACTIVE]);
        }

        return $changed;
    }

    private function hideProductsMissingLocalImages(bool $dryRun): int
    {
        $query = Product::query()
            ->where('source_type', 'supermarket')
            ->whereDoesntHave('media', fn ($query) => $query->where('collection_name', 'product'));

        $count = (clone $query)->count();

        if (!$dryRun) {
            $query->update(['status' => Status::INACTIVE]);
            Product::query()
                ->where('source_type', 'supermarket')
                ->whereHas('media', fn ($query) => $query->where('collection_name', 'product'))
                ->update(['status' => Status::ACTIVE]);
        }

        return $count;
    }

    private function refreshProductFromSources(int $productId, float $margin): void
    {
        $sources = SupermarketProductSource::query()
            ->where('product_id', $productId)
            ->where('source_available', true)
            ->whereNotNull('source_price')
            ->where('source_price', '>', 0)
            ->orderBy('source_price')
            ->get();

        if ($sources->isEmpty()) {
            return;
        }

        $basePrice = (float) $sources->min('source_price');
        $sellingPrice = round($basePrice * (1 + ($margin / 100)), 2);
        $imageUrl = $sources->first(fn ($source) => !empty($source->source_image_url))?->source_image_url;

        $payload = [
            'supermarket_base_price' => $basePrice,
            'supermarket_margin_percent' => $margin,
            'supermarket_candidate_count' => $sources->count(),
            'supermarket_available' => $sources->contains(fn ($source) => (bool) $source->source_available),
            'supermarket_available_quantity' => $sources->sum('source_available_quantity') ?: null,
            'supermarket_synced_at' => now(),
            'external_image_url' => $imageUrl,
        ];

        $product = Product::query()->whereKey($productId)->first();
        if (!$product?->manual_price_override) {
            $payload['buying_price'] = $basePrice;
            $payload['selling_price'] = $sellingPrice;
            $payload['variation_price'] = $sellingPrice;
        }

        Product::query()->whereKey($productId)->update($payload);
    }

    private function categoryNameFor(Product $product): string
    {
        $paths = SupermarketProductSource::query()
            ->where('product_id', $product->id)
            ->pluck('source_category_path')
            ->flatMap(fn ($path) => is_array($path) ? $path : [])
            ->implode(' ');

        $text = Str::lower($product->name . ' ' . $paths);

        foreach (self::CATEGORY_RULES as $category => $keywords) {
            foreach ($keywords as $keyword) {
                if (Str::contains($text, $keyword)) {
                    return $category;
                }
            }
        }

        return 'Other';
    }

    private function duplicateKey(Product $product): string
    {
        $name = Str::of($product->name)
            ->ascii()
            ->lower()
            ->replaceMatches('/\b(offer|new|promo|sale|online|buy)\b/', ' ')
            ->replaceMatches('/(\d)\s*(gm|gram|grams)\b/', '$1 g')
            ->replaceMatches('/(\d)\s*(ltr|liter|litre)\b/', '$1 l')
            ->replaceMatches('/(\d)\s*(ml|kg|g|l|pcs|pc|piece|pieces|pack|packs)\b/', '$1 $2')
            ->replaceMatches('/[^\pL\pN]+/u', ' ')
            ->squish()
            ->toString();

        if ($name === '') {
            return '';
        }

        return (string) $product->product_brand_id . '|' . $name;
    }

    private function printCoverage(): void
    {
        $total = Product::query()->where('source_type', 'supermarket')->whereNull('deleted_at')->count();
        $active = Product::query()->where('source_type', 'supermarket')->whereNull('deleted_at')->where('status', Status::ACTIVE)->count();
        $withLocalImage = Product::query()
            ->where('source_type', 'supermarket')
            ->whereNull('deleted_at')
            ->whereHas('media', fn ($query) => $query->where('collection_name', 'product'))
            ->count();
        $categoriesUsed = Product::query()
            ->where('source_type', 'supermarket')
            ->whereNull('deleted_at')
            ->distinct('product_category_id')
            ->count('product_category_id');

        $this->info(sprintf(
            'Coverage after repair: %s total, %s active, %s with local images, %s categories used.',
            number_format($total),
            number_format($active),
            number_format($withLocalImage),
            number_format($categoriesUsed)
        ));
    }
}
