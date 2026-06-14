<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\Response;

class ImageProxyController extends Controller
{
    public function __invoke(Request $request): Response
    {
        $url = (string) $request->query('url', '');
        $parts = parse_url($url);

        if (
            !$parts ||
            !in_array($parts['scheme'] ?? '', ['http', 'https'], true) ||
            empty($parts['host'])
        ) {
            return $this->fallback();
        }

        $cacheKey = sha1($url);
        $path = "proxied-product-images/{$cacheKey}";

        if (!Storage::disk('public')->exists($path)) {
            try {
                $response = Http::timeout(20)
                    ->retry(2, 250)
                    ->withHeaders([
                        'User-Agent' => 'Mozilla/5.0 (compatible; GEMONA-IDS/1.0)',
                        'Accept' => 'image/avif,image/webp,image/apng,image/svg+xml,image/*,*/*;q=0.8',
                    ])
                    ->get($url);

                if (!$response->successful() || !str_starts_with((string) $response->header('Content-Type'), 'image/')) {
                    return $this->fallback();
                }

                Storage::disk('public')->put($path, $response->body());
                Storage::disk('public')->put("{$path}.content-type", (string) $response->header('Content-Type', 'image/jpeg'));
            } catch (\Throwable) {
                return $this->fallback();
            }
        }

        $contentType = Storage::disk('public')->exists("{$path}.content-type")
            ? Storage::disk('public')->get("{$path}.content-type")
            : 'image/jpeg';

        return response(Storage::disk('public')->get($path), 200, [
            'Content-Type' => $contentType,
            'Cache-Control' => 'public, max-age=604800, immutable',
        ]);
    }

    private function fallback(): Response
    {
        return response()->file(public_path('images/default/product/cover.png'), [
            'Cache-Control' => 'public, max-age=3600',
        ]);
    }
}
