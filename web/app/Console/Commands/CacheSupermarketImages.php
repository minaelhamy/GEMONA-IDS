<?php

namespace App\Console\Commands;

use App\Models\Product;
use App\Models\SupermarketProductSource;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;
use Throwable;

class CacheSupermarketImages extends Command
{
    private const REJECTED_IMAGE_HASHES = [
        // HyperOne logo placeholder returned from some product-image URLs.
        '0260f5b89cb272c22dc63bb7416e8088be9aae5cd1b0879a4f18ee0f5d9dca90',
    ];

    protected $signature = 'supermarket:cache-images
        {--limit= : Maximum number of products to process in this run}
        {--force : Replace existing imported supermarket product images}
        {--progress-every=100 : Print progress after this many products}';

    protected $description = 'Download supermarket product images into the local product media library.';

    public function handle(): int
    {
        $limit = $this->option('limit') !== null ? max(0, (int) $this->option('limit')) : null;
        $force = (bool) $this->option('force');
        $progressEvery = max(1, (int) $this->option('progress-every'));
        $tempDir = storage_path('app/supermarket-image-imports');

        File::ensureDirectoryExists($tempDir);

        $query = Product::query()
            ->select(['id', 'name', 'sku', 'external_image_url'])
            ->where('source_type', 'supermarket')
            ->orderBy('id');

        if (!$force) {
            $query->whereDoesntHave('media', fn ($media) => $media->where('collection_name', 'product'));
        }

        if ($limit !== null && $limit > 0) {
            $query->limit($limit);
        }

        $total = (clone $query)->count();
        $this->info(sprintf('Caching local images for %s supermarket products.', number_format($total)));

        $processed = 0;
        $cached = 0;
        $failed = 0;
        $skipped = 0;

        $query->chunkById(100, function ($products) use ($force, $progressEvery, $tempDir, &$processed, &$cached, &$failed, &$skipped) {
            foreach ($products as $product) {
                $processed++;

                try {
                    $imageUrls = $this->imageUrlsFor($product);
                    if (!$imageUrls) {
                        $skipped++;
                        continue;
                    }

                    if ($force) {
                        $product->clearMediaCollection('product');
                    }

                    $tempPath = null;
                    $cachedUrl = null;
                    foreach ($imageUrls as $imageUrl) {
                        $tempPath = $this->downloadImage($imageUrl, $tempDir, (string) $product->sku);
                        if ($tempPath) {
                            $cachedUrl = $imageUrl;
                            break;
                        }
                    }

                    if (!$tempPath) {
                        $failed++;
                        continue;
                    }

                    $product
                        ->addMedia($tempPath)
                        ->usingFileName(basename($tempPath))
                        ->withCustomProperties(['source_url' => $cachedUrl, 'source_type' => 'supermarket'])
                        ->toMediaCollection('product');

                    $cached++;
                } catch (Throwable $exception) {
                    $failed++;
                    $this->warn(sprintf('Image failed for product #%s: %s', $product->id, $exception->getMessage()));
                }

                if ($processed % $progressEvery === 0) {
                    $this->line(sprintf(
                        '%s processed, %s cached, %s failed, %s skipped...',
                        number_format($processed),
                        number_format($cached),
                        number_format($failed),
                        number_format($skipped)
                    ));
                }
            }
        });

        File::deleteDirectory($tempDir);

        $this->info(sprintf(
            'Done. Processed: %s, cached: %s, failed: %s, skipped: %s.',
            number_format($processed),
            number_format($cached),
            number_format($failed),
            number_format($skipped)
        ));

        return self::SUCCESS;
    }

    private function imageUrlsFor(Product $product): array
    {
        $urls = [];
        $primaryUrl = trim((string) $product->external_image_url);
        if ($primaryUrl !== '') {
            $urls[] = $primaryUrl;
        }

        SupermarketProductSource::query()
            ->where('product_id', $product->id)
            ->whereNotNull('source_image_url')
            ->where('source_image_url', '!=', '')
            ->orderBy('source_available', 'desc')
            ->orderBy('source_price')
            ->pluck('source_image_url')
            ->each(function ($url) use (&$urls) {
                $url = trim((string) $url);
                if ($url !== '') {
                    $urls[] = $url;
                }
            });

        return array_values(array_unique($urls));
    }

    private function downloadImage(string $url, string $tempDir, string $sku): ?string
    {
        $response = Http::withHeaders([
            'User-Agent' => 'Mozilla/5.0 (compatible; GEMONA IDS catalog image importer)',
            'Accept' => 'image/avif,image/webp,image/apng,image/svg+xml,image/*,*/*;q=0.8',
        ])
            ->connectTimeout(5)
            ->timeout(20)
            ->retry(2, 300)
            ->get($url);

        if (!$response->successful() || $response->body() === '') {
            return null;
        }

        if (in_array(hash('sha256', $response->body()), self::REJECTED_IMAGE_HASHES, true)) {
            return null;
        }

        $extension = $this->extensionFor($url, (string) $response->header('Content-Type'));
        $filename = Str::slug($sku ?: sha1($url)) . '-' . substr(sha1($url), 0, 12) . '.' . $extension;
        $path = $tempDir . DIRECTORY_SEPARATOR . $filename;

        File::put($path, $response->body());

        return $path;
    }

    private function extensionFor(string $url, string $contentType): string
    {
        $contentType = Str::lower(strtok($contentType, ';') ?: '');
        $map = [
            'image/jpeg' => 'jpg',
            'image/jpg' => 'jpg',
            'image/png' => 'png',
            'image/webp' => 'webp',
            'image/gif' => 'gif',
            'image/svg+xml' => 'svg',
            'image/avif' => 'avif',
        ];

        if (isset($map[$contentType])) {
            return $map[$contentType];
        }

        $path = parse_url($url, PHP_URL_PATH) ?: '';
        $extension = Str::lower(pathinfo($path, PATHINFO_EXTENSION));

        return in_array($extension, ['jpg', 'jpeg', 'png', 'webp', 'gif', 'svg', 'avif'], true)
            ? ($extension === 'jpeg' ? 'jpg' : $extension)
            : 'jpg';
    }
}
