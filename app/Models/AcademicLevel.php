<?php

namespace App\Models;

use App\Enums\CurriculumType;
use App\Models\Concerns\HasSlug;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable([
    'name_ar', 'name_en', 'slug', 'number', 'curriculum_type',
    'description_ar', 'description_en', 'sort_order', 'is_active',
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
            'curriculum_type' => CurriculumType::class,
        ];
    }

    public function isGeneral(): bool
    {
        return $this->curriculum_type === CurriculumType::General;
    }

    public function isSpecialized(): bool
    {
        return $this->curriculum_type === CurriculumType::Specialized;
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
