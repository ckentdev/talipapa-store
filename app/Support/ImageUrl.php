<?php

namespace App\Support;

use Illuminate\Support\Facades\Storage;

class ImageUrl
{
    public static function for(?string $path, string $type = 'product'): string
    {
        return self::resolve($path) ?? asset(config("images.defaults.{$type}", config('images.defaults.product')));
    }

    public static function resolve(?string $path): ?string
    {
        if (blank($path)) {
            return null;
        }

        if (str_starts_with($path, 'img/')) {
            return asset($path);
        }

        if (str_starts_with($path, 'http://') || str_starts_with($path, 'https://') || str_starts_with($path, '//')) {
            return $path;
        }

        return Storage::url($path);
    }

    public static function fallback(string $type = 'product'): string
    {
        return asset(config("images.defaults.{$type}", config('images.defaults.product')));
    }
}
