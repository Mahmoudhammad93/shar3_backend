<?php

namespace App\Http\Controllers\Api;

use App\Actions\SubmitLessonQuizAction;
use App\Http\Controllers\Api\Concerns\ResolvesAuthenticatedStudent;
use App\Http\Controllers\Controller;
use App\Models\Lesson;
use App\Models\LessonProgress;
use App\Models\LessonQuestion;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class StudentQuizController extends Controller
{
    use ResolvesAuthenticatedStudent;

    public function __construct(private readonly SubmitLessonQuizAction $submitLessonQuiz) {}

    public function show(Request $request, int $lessonId): JsonResponse
    {
        $student = $this->student($request);
        $lesson = Lesson::query()->with(['questions.options'])->findOrFail($lessonId);

        $this->ensureLessonAccessible($student, $lesson);

        abort_unless($lesson->questions->isNotEmpty(), 404, 'لا توجد أسئلة لهذا الدرس');

        return response()->json([
            'lesson_id' => $lesson->id,
            'questions' => $lesson->questions->map(fn (LessonQuestion $question) => [
                'id' => $question->id,
                'type' => $question->type ?? LessonQuestion::TYPE_CHOICE,
                'question_ar' => $question->question_ar,
                'options' => $question->isChoice()
                    ? $question->options->map(fn ($option) => [
                        'id' => $option->id,
                        'option_ar' => $option->option_ar,
                    ])->values()
                    : [],
            ]),
        ]);
    }

    public function submit(Request $request, int $lessonId): JsonResponse
    {
        $student = $this->student($request);
        $lesson = Lesson::query()->with(['questions.options'])->findOrFail($lessonId);

        $this->ensureLessonAccessible($student, $lesson);

        $validated = $request->validate([
            'answers' => ['required', 'array'],
            'answers.*' => ['required'],
        ]);

        $questions = $lesson->questions;

        abort_unless($questions->isNotEmpty(), 404);

        $questionIds = $questions->pluck('id')->all();
        $submittedQuestionIds = array_map('intval', array_keys($validated['answers']));

        abort_unless(
            count($submittedQuestionIds) === count($questionIds)
            && empty(array_diff($submittedQuestionIds, $questionIds)),
            422,
            'يجب الإجابة على جميع أسئلة الدرس.'
        );

        foreach ($questions as $question) {
            $answer = $validated['answers'][$question->id] ?? null;

            if ($question->isChoice()) {
                $optionId = (int) $answer;
                $validOption = $question->options->contains('id', $optionId);
                abort_unless($validOption, 422, 'إجابة غير صالحة.');
            }

            if (! $this->submitLessonQuiz->isAnswerCorrect($question, $answer)) {
                return response()->json([
                    'message' => 'إجابة غير صحيحة. راجع الدرس وحاول مرة أخرى.',
                    'passed' => false,
                ], 422);
            }
        }

        LessonProgress::query()->updateOrCreate(
            ['student_id' => $student->id, 'lesson_id' => $lessonId],
            ['quiz_passed' => true]
        );

        return response()->json([
            'message' => 'أحسنت! اجتزت أسئلة الدرس.',
            'passed' => true,
        ]);
    }
}
