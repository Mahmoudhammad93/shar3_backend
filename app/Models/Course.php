<?php

namespace App\Models;

use App\Models\Concerns\HasSlug;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

#[Fillable([
    'category_id', 'program_id', 'teacher_id', 'title_ar', 'title_en', 'slug',
    'description_ar', 'description_en', 'image', 'duration_hours', 'level',
    'price', 'is_free', 'is_featured', 'is_published', 'sort_order', 'start_date', 'end_date',
])]
class Course extends Model
{
    use HasSlug;

    protected static function slugSource(): string
    {
        return 'title_ar';
    }

    protected function casts(): array
    {
        return [
            'is_free' => 'boolean',
            'is_featured' => 'boolean',
            'is_published' => 'boolean',
            'price' => 'decimal:2',
            'start_date' => 'date',
            'end_date' => 'date',
        ];
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function program(): BelongsTo
    {
        return $this->belongsTo(Program::class);
    }

    public function teacher(): BelongsTo
    {
        return $this->belongsTo(Teacher::class);
    }

    public function lessons(): HasMany
    {
        return $this->hasMany(Lesson::class)->orderBy('sort_order');
    }

    public function enrollments(): HasMany
    {
        return $this->hasMany(Enrollment::class);
    }

    public function subject(): HasOne
    {
        return $this->hasOne(Subject::class);
    }
}
