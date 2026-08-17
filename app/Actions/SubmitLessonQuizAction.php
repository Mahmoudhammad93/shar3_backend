<?php

namespace App\Actions;

use App\Models\LessonQuestion;
use App\Models\LessonQuestionOption;

class SubmitLessonQuizAction
{
    public function isAnswerCorrect(LessonQuestion $question, mixed $answer): bool
    {
        if ($answer === null || $answer === '') {
            return false;
        }

        return match ($question->type ?? LessonQuestion::TYPE_CHOICE) {
            LessonQuestion::TYPE_TRUE_FALSE => $this->normalizeTrueFalseAnswer($answer) === $question->correct_answer,
            LessonQuestion::TYPE_TEXT => $this->normalizeQuizText((string) $answer) === $this->normalizeQuizText((string) $question->correct_answer),
            default => $this->isChoiceAnswerCorrect($question, $answer),
        };
    }

    public function isChoiceAnswerCorrect(LessonQuestion $question, mixed $answer): bool
    {
        $optionId = (int) $answer;

        return LessonQuestionOption::query()
            ->where('lesson_question_id', $question->id)
            ->where('id', $optionId)
            ->where('is_correct', true)
            ->exists();
    }

    private function normalizeTrueFalseAnswer(mixed $answer): ?string
    {
        if ($answer === true || $answer === 'true' || $answer === '1' || $answer === 1) {
            return 'true';
        }

        if ($answer === false || $answer === 'false' || $answer === '0' || $answer === 0) {
            return 'false';
        }

        return null;
    }

    private function normalizeQuizText(string $text): string
    {
        $text = mb_strtolower(trim($text), 'UTF-8');
        $text = str_replace(['أ', 'إ', 'آ'], 'ا', $text);

        return preg_replace('/\s+/u', ' ', $text) ?? $text;
    }
}
