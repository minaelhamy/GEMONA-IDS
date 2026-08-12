<?php

namespace App\Console\Commands;

use App\Enums\Status;
use App\Models\Product;
use App\Models\SupermarketProductSource;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class ReplaceCatalogFromStage extends Command
{
    protected $signature = 'catalog:replace-staged
        {stage : Absolute path to the validated stage directory}
        {--confirm= : Must be REPLACE-GEMONA-CATALOG}
        {--reuse-imported : Verify and finalize a previously completed staged import}
        {--margin=15 : Selling-price margin percentage}';

    protected $description = 'Import a fully staged image-backed catalog, verify it, then archive the previous catalog.';

    public function handle(): int
    {
        if ($this->option('confirm') !== 'REPLACE-GEMONA-CATALOG') {
            $this->error('Refusing replacement without --confirm=REPLACE-GEMONA-CATALOG.');
            return self::FAILURE;
        }

        $stage = rtrim((string) $this->argument('stage'), DIRECTORY_SEPARATOR);
        $productsPath = $stage . '/products.jsonl';
        $clustersPath = $stage . '/clusters.json';
        $manifestPath = $stage . '/manifest.json';
        $manifest = is_file($manifestPath) ? json_decode(file_get_contents($manifestPath), true) : null;
        if (!is_array($manifest) || !is_file($productsPath) || !is_file($clustersPath)) {
            $this->error('The staged catalog is incomplete.');
            return self::FAILURE;
        }
        if (array_diff($manifest['sources'] ?? [], ['seoudi', 'hyperone', 'btech'])) {
            $this->error('The staged catalog contains a source outside seoudi, hyperone, and btech.');
            return self::FAILURE;
        }
        if (!hash_equals((string) ($manifest['products_sha256'] ?? ''), hash_file('sha256', $productsPath))
            || !hash_equals((string) ($manifest['clusters_sha256'] ?? ''), hash_file('sha256', $clustersPath))) {
            $this->error('The staged catalog manifest checksums do not match.');
            return self::FAILURE;
        }

        $clusters = json_decode(file_get_contents($clustersPath), true);
        $expectedKeys = collect($clusters)->map(fn ($cluster) => 'supermarket:' . $cluster['cluster_id'])->values();
        if ($expectedKeys->count() !== (int) ($manifest['cluster_count'] ?? -1) || $expectedKeys->isEmpty()) {
            $this->error('The staged cluster count is invalid.');
            return self::FAILURE;
        }

        $oldProducts = Product::query()->get(['id', 'external_key']);
        if (!$this->option('reuse-imported')) {
            $this->info('Importing and locally attaching the staged catalog before retiring existing products.');
            $result = $this->call('supermarket:import', [
                '--products' => $productsPath,
                '--clusters' => $clustersPath,
                '--margin' => (float) $this->option('margin'),
                '--require-local-images' => true,
                '--skip-media-conversions' => true,
            ]);
            if ($result !== self::SUCCESS) {
                $this->error('New catalog import failed. Existing products remain available.');
                return $result;
            }
        } else {
            $this->info('Reusing the completed staged import; all safety gates will run before retirement.');
        }

        $verified = 0;
        foreach ($expectedKeys->chunk(500) as $keys) {
            $verified += Product::query()
                ->whereIn('external_key', $keys)
                ->where('status', Status::ACTIVE)
                ->whereHas('media', fn ($query) => $query->where('collection_name', 'product'))
                ->count();
        }
        if ($verified !== $expectedKeys->count()) {
            $this->error("Only {$verified} of {$expectedKeys->count()} staged products passed post-import media verification. Existing products remain available.");
            return self::FAILURE;
        }
        $marginMultiplier = 1 + ((float) $this->option('margin') / 100);
        $priceErrors = 0;
        foreach ($expectedKeys->chunk(500) as $keys) {
            $priceErrors += Product::query()
                ->whereIn('external_key', $keys)
                ->whereRaw(
                    'ABS(selling_price - ROUND(supermarket_base_price * CAST(? AS DECIMAL(10,4)), 2)) > 0.009',
                    [number_format($marginMultiplier, 4, '.', '')]
                )
                ->count();
        }
        if ($priceErrors > 0) {
            $this->error("{$priceErrors} staged products failed the selling-price margin check. Existing products remain available.");
            return self::FAILURE;
        }

        $expectedLookup = $expectedKeys->flip();
        $oldProducts = $oldProducts
            ->reject(fn ($product) => $expectedLookup->has((string) $product->external_key))
            ->map(fn ($product) => Product::find($product->id))
            ->filter();
        $oldIds = $oldProducts->pluck('id');
        foreach ($oldProducts as $oldProduct) {
            $oldProduct->clearMediaCollection('product');
        }
        DB::transaction(function () use ($oldProducts, $oldIds) {
            SupermarketProductSource::whereIn('product_id', $oldIds)->delete();
            foreach ($oldProducts as $oldProduct) {
                $oldProduct->status = Status::INACTIVE;
                $oldProduct->save();
                $oldProduct->delete();
            }
        });

        $this->info(sprintf(
            'Replacement complete: %s active image-backed products; %s previous products archived and their media removed.',
            number_format($verified),
            number_format($oldProducts->count())
        ));
        return self::SUCCESS;
    }
}
