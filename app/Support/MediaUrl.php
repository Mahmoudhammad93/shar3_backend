<?php

namespace App\Support;

use Illuminate\Support\Facades\Storage;

class MediaUrl
{
    public static function resolve(?string $path): ?string
    {
        if (! $path) {
            return null;
        }

        if (str_starts_with($path, 'http://') || str_starts_with($path, 'https://')) {
            return $path;
        }

        $normalized = ltrim($path, '/');

        foreach (['public', 'local'] as $disk) {
            if (Storage::disk($disk)->exists($normalized)) {
                return rtrim(config('app.url'), '/').'/media/'.$normalized;
            }
        }

        return null;
    }
}
