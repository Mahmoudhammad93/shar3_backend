<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable([
    'course_id', 'title_ar', 'title_en', 'content_ar', 'content_en',
    'video_url', 'duration_minutes', 'sort_order', 'is_published',
])]
class Lesson extends Model
{
    protected function casts(): array
    {
        return [
            'is_published' => 'boolean',
            'sort_order' => 'integer',
            'duration_minutes' => 'integer',
        ];
    }

    public function course(): BelongsTo
    {
        return $this->belongsTo(Course::class);
    }

    public function progress(): HasMany
    {
        return $this->hasMany(LessonProgress::class);
    }

    public function questions(): HasMany
    {
        return $this->hasMany(LessonQuestion::class)->orderBy('sort_order');
    }

    public function hasQuiz(): bool
    {
        return $this->questions()->exists();
    }
}
