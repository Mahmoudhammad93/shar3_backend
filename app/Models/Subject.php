<?php

namespace App\Models;

use App\Models\Concerns\HasSlug;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable([
    'semester_id', 'specialization_id', 'course_id', 'name_ar', 'name_en', 'slug',
    'description_ar', 'description_en', 'sort_order', 'is_required', 'is_active',
    'memorization_ar', 'memorization_en', 'primary_text_ar', 'primary_text_en',
    'supplementary_text_ar', 'supplementary_text_en',
])]
class Subject extends Model
{
    use HasSlug;

    protected function casts(): array
    {
        return [
            'sort_order' => 'integer',
            'is_required' => 'boolean',
            'is_active' => 'boolean',
        ];
    }

    public function semester(): BelongsTo
    {
        return $this->belongsTo(Semester::class);
    }

    public function specialization(): BelongsTo
    {
        return $this->belongsTo(Specialization::class);
    }

    public function course(): BelongsTo
    {
        return $this->belongsTo(Course::class);
    }
}
