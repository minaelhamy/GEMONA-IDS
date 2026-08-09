<?php

namespace App\Console\Commands;

use App\Enums\Activity;
use App\Enums\Ask;
use App\Models\Product;
use App\Models\SupermarketProductSource;
use Illuminate\Console\Command;

class RepairSupermarketCatalog extends Command
{
    protected $signature = 'gemona:repair-supermarket-catalog';

    protected $description = 'Repair imported supermarket products so they remain orderable and use the best available source image.';

    public function handle(): int
    {
        $orderable = Product::query()
            ->where('source_type', 'supermarket')
            ->update([
                'can_purchasable' => Ask::YES,
                'show_stock_out' => Activity::DISABLE,
            ]);

        $imagesFilled = 0;
        Product::query()
            ->where('source_type', 'supermarket')
            ->where(function ($query) {
                $query->whereNull('external_image_url')->orWhere('external_image_url', '');
            })
            ->select(['id'])
            ->chunkById(500, function ($products) use (&$imagesFilled) {
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

                    Product::query()
                        ->whereKey($product->id)
                        ->update(['external_image_url' => $imageUrl]);
                    $imagesFilled++;
                }
            });

        $total = Product::query()->where('source_type', 'supermarket')->count();
        $withLocalImage = Product::query()
            ->where('source_type', 'supermarket')
            ->whereHas('media', fn ($query) => $query->where('collection_name', 'product'))
            ->count();
        $withSourceImage = Product::query()
            ->where('source_type', 'supermarket')
            ->whereNotNull('external_image_url')
            ->where('external_image_url', '!=', '')
            ->count();
        $withoutAnySourceImage = Product::query()
            ->where('source_type', 'supermarket')
            ->whereDoesntHave('media', fn ($query) => $query->where('collection_name', 'product'))
            ->where(function ($query) {
                $query->whereNull('external_image_url')->orWhere('external_image_url', '');
            })
            ->count();

        $this->info(sprintf('Supermarket products repaired: %s.', number_format($orderable)));
        $this->info(sprintf('Missing external image URLs filled from source candidates: %s.', number_format($imagesFilled)));
        $this->info(sprintf(
            'Image coverage: %s total, %s local media, %s with source image URL, %s without any source image.',
            number_format($total),
            number_format($withLocalImage),
            number_format($withSourceImage),
            number_format($withoutAnySourceImage)
        ));

        return self::SUCCESS;
    }
}
