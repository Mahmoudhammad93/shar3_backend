<?php

namespace App\Models;

use App\Models\Concerns\HasSlug;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable([
    'name_ar', 'name_en', 'slug', 'title_ar', 'title_en', 'bio_ar', 'bio_en',
    'specializations', 'photo', 'email', 'phone', 'is_featured', 'is_active', 'sort_order',
])]
class Teacher extends Model
{
    use HasSlug;

    protected static function slugSource(): string
    {
        return 'name_ar';
    }

    protected function casts(): array
    {
        return [
            'is_featured' => 'boolean',
            'is_active' => 'boolean',
            'sort_order' => 'integer',
        ];
    }

    public function courses(): HasMany
    {
        return $this->hasMany(Course::class);
    }
}
