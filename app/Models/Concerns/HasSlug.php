<?php

namespace App\Models\Concerns;

use Illuminate\Support\Str;

trait HasSlug
{
    protected static function bootHasSlug(): void
    {
        static::creating(function ($model) {
            $source = $model->{static::slugSource()} ?? null;
            $rawSlug = $model->slug ?? null;

            if (filled($rawSlug)) {
                $base = trim((string) $rawSlug);
            } elseif (filled($source)) {
                $base = (string) $source;
            } else {
                return;
            }

            $model->slug = static::generateUniqueSlug($base);
        });

        static::updating(function ($model) {
            if ($model->isDirty('slug')) {
                $model->slug = static::generateUniqueSlug(
                    trim((string) $model->slug),
                    $model->getKey(),
                );

                return;
            }

            if ($model->isDirty(static::slugSource())) {
                $model->slug = static::generateUniqueSlug(
                    (string) $model->{static::slugSource()},
                    $model->getKey(),
                );
            }
        });
    }

    protected static function slugSource(): string
    {
        return 'name_ar';
    }

    public static function generateUniqueSlug(string $value, ?int $ignoreId = null): string
    {
        $slug = Str::slug(trim($value));
        if ($slug === '') {
            $slug = 'item-'.Str::lower(Str::random(8));
        }
        $original = $slug;
        $counter = 1;

        while (static::query()
            ->when($ignoreId, fn ($q) => $q->where('id', '!=', $ignoreId))
            ->where('slug', $slug)
            ->exists()) {
            $slug = $original.'-'.$counter++;
        }

        return $slug;
    }
}
