<?php

namespace App\Support;

use Illuminate\Support\Str;

class SupermarketImageGuard
{
    private const REJECTED_IMAGE_HASHES = [
        // HyperOne logo placeholder returned from some product-image URLs.
        '0260f5b89cb272c22dc63bb7416e8088be9aae5cd1b0879a4f18ee0f5d9dca90',
    ];

    private const REJECTED_URL_PATTERNS = [
        '/images/default/',
        '/placeholder/',
        'placeholder',
        'no-image',
        'no_image',
        'not-found',
    ];

    public static function cleanUrl(?string $url): ?string
    {
        $url = trim((string) $url);
        if ($url === '' || self::isRejectedUrl($url)) {
            return null;
        }

        return $url;
    }

    public static function firstUsableUrl(array $urls): ?string
    {
        foreach ($urls as $url) {
            $url = self::cleanUrl(is_string($url) ? $url : null);
            if ($url !== null) {
                return $url;
            }
        }

        return null;
    }

    public static function isRejectedBody(string $body): bool
    {
        return in_array(hash('sha256', $body), self::REJECTED_IMAGE_HASHES, true);
    }

    public static function isRejectedFile(string $path): bool
    {
        return is_file($path) && in_array(hash_file('sha256', $path), self::REJECTED_IMAGE_HASHES, true);
    }

    public static function isRejectedUrl(string $url): bool
    {
        $url = Str::lower(trim($url));
        if ($url === '') {
            return true;
        }

        foreach (self::REJECTED_URL_PATTERNS as $pattern) {
            if (Str::contains($url, $pattern)) {
                return true;
            }
        }

        return false;
    }
}
