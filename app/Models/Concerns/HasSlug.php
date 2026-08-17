<?php

namespace App\Models\Concerns;

use Illuminate\Support\Str;

trait HasSlug
{
    protected static function bootHasSlug(): void
    {
        static::creating(function ($model) {
            if (empty($model->slug) && ! empty($model->{static::slugSource()})) {
                $model->slug = static::generateUniqueSlug($model->{static::slugSource()});
            }
        });

        static::updating(function ($model) {
            if ($model->isDirty(static::slugSource()) && ! $model->isDirty('slug')) {
                $model->slug = static::generateUniqueSlug($model->{static::slugSource()}, $model->getKey());
            }
        });
    }

    protected static function slugSource(): string
    {
        return 'name_ar';
    }

    protected static function generateUniqueSlug(string $value, ?int $ignoreId = null): string
    {
        $slug = Str::slug($value);
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
