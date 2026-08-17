<?php

namespace App\Models;

use App\Models\Concerns\HasSlug;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable([
    'name_ar', 'name_en', 'slug', 'description_ar', 'description_en',
    'course_id', 'is_active',
    'memorization_ar', 'memorization_en', 'primary_text_ar', 'primary_text_en',
    'supplementary_text_ar', 'supplementary_text_en',
])]
class Subject extends Model
{
    use HasSlug;

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
        ];
    }

    public function course(): BelongsTo
    {
        return $this->belongsTo(Course::class);
    }

    public function curriculumAssignments(): HasMany
    {
        return $this->hasMany(CurriculumAssignment::class);
    }
}
