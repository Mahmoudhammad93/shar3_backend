<?php

namespace App\Models;

use App\Models\Concerns\HasSlug;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable([
    'academic_year_id', 'name_ar', 'name_en', 'slug', 'semester_number',
    'starts_at', 'ends_at', 'sort_order', 'is_active',
])]
class Semester extends Model
{
    use HasSlug;

    protected function casts(): array
    {
        return [
            'semester_number' => 'integer',
            'sort_order' => 'integer',
            'is_active' => 'boolean',
            'starts_at' => 'date',
            'ends_at' => 'date',
        ];
    }

    public function year(): BelongsTo
    {
        return $this->belongsTo(AcademicYear::class, 'academic_year_id');
    }

    public function curriculumAssignments(): HasMany
    {
        return $this->hasMany(CurriculumAssignment::class)->orderBy('sort_order');
    }
}
