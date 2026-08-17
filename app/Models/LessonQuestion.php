<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable(['lesson_id', 'question_ar', 'type', 'correct_answer', 'sort_order'])]
#[Hidden(['correct_answer'])]
class LessonQuestion extends Model
{
    public const TYPE_CHOICE = 'choice';

    public const TYPE_TRUE_FALSE = 'true_false';

    public const TYPE_TEXT = 'text';

    /** @return array<string, string> */
    public static function typeOptions(): array
    {
        return [
            self::TYPE_CHOICE => 'اختيار من متعدد',
            self::TYPE_TRUE_FALSE => 'صح / خطأ',
            self::TYPE_TEXT => 'إجابة كتابية',
        ];
    }

    public function typeLabel(): string
    {
        return self::typeOptions()[$this->type] ?? $this->type;
    }

    protected function casts(): array
    {
        return [
            'sort_order' => 'integer',
        ];
    }

    public function lesson(): BelongsTo
    {
        return $this->belongsTo(Lesson::class);
    }

    public function options(): HasMany
    {
        return $this->hasMany(LessonQuestionOption::class)->orderBy('sort_order');
    }

    public function isChoice(): bool
    {
        return $this->type === self::TYPE_CHOICE;
    }

    public function isTrueFalse(): bool
    {
        return $this->type === self::TYPE_TRUE_FALSE;
    }

    public function isText(): bool
    {
        return $this->type === self::TYPE_TEXT;
    }
}
