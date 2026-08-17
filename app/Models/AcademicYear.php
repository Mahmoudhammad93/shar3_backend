<?php

namespace App\Models;

use App\Models\Concerns\HasSlug;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable([
    'academic_level_id', 'name_ar', 'name_en', 'slug', 'year_number',
    'description_ar', 'description_en', 'sort_order', 'is_active',
])]
class AcademicYear extends Model
{
    use HasSlug;

    protected function casts(): array
    {
        return [
            'year_number' => 'integer',
            'sort_order' => 'integer',
            'is_active' => 'boolean',
        ];
    }

    public function level(): BelongsTo
    {
        return $this->belongsTo(AcademicLevel::class, 'academic_level_id');
    }

    public function semesters(): HasMany
    {
        return $this->hasMany(Semester::class)->orderBy('sort_order');
    }
}
