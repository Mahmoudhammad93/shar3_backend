<?php

namespace App\Models;

use App\Models\Concerns\HasSlug;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable([
    'name_ar', 'name_en', 'slug', 'number', 'description_ar', 'description_en',
    'sort_order', 'is_active',
])]
class AcademicLevel extends Model
{
    use HasSlug;

    protected function casts(): array
    {
        return [
            'number' => 'integer',
            'sort_order' => 'integer',
            'is_active' => 'boolean',
        ];
    }

    public function years(): HasMany
    {
        return $this->hasMany(AcademicYear::class)->orderBy('sort_order');
    }

    public function specializations(): HasMany
    {
        return $this->hasMany(Specialization::class)->orderBy('sort_order');
    }
}
