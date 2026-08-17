<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable(['student_id', 'lesson_id', 'progress_percent', 'quiz_passed', 'is_completed', 'completed_at'])]
class LessonProgress extends Model
{
    protected $table = 'lesson_progress';

    protected function casts(): array
    {
        return [
            'is_completed' => 'boolean',
            'quiz_passed' => 'boolean',
            'completed_at' => 'datetime',
        ];
    }

    public function student(): BelongsTo
    {
        return $this->belongsTo(Student::class);
    }

    public function lesson(): BelongsTo
    {
        return $this->belongsTo(Lesson::class);
    }
}
