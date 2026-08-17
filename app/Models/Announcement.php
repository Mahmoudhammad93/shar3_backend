<?php

namespace App\Models;

use App\Models\Concerns\HasSlug;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;

#[Fillable([
    'title_ar', 'title_en', 'slug', 'excerpt_ar', 'excerpt_en',
    'content_ar', 'content_en', 'image', 'is_published', 'published_at',
])]
class Announcement extends Model
{
    use HasSlug;

    protected static function slugSource(): string
    {
        return 'title_ar';
    }

    protected function casts(): array
    {
        return [
            'is_published' => 'boolean',
            'published_at' => 'datetime',
        ];
    }
}
