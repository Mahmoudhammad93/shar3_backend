<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Api\Concerns\ResolvesAuthenticatedStudent;
use App\Http\Controllers\Controller;
use App\Models\Lesson;
use App\Models\LessonProgress;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class StudentLessonController extends Controller
{
    use ResolvesAuthenticatedStudent;

    public function updateProgress(Request $request, int $lessonId): JsonResponse
    {
        $student = $this->student($request);
        $lesson = Lesson::query()->findOrFail($lessonId);

        $validated = $request->validate([
            'progress_percent' => ['required', 'integer', 'min:0', 'max:100'],
        ]);

        $this->ensureLessonAccessible($student, $lesson);

        $existing = LessonProgress::query()
            ->where('student_id', $student->id)
            ->where('lesson_id', $lessonId)
            ->first();

        $progressPercent = max(
            $existing?->progress_percent ?? 0,
            $validated['progress_percent']
        );

        LessonProgress::query()->updateOrCreate(
            ['student_id' => $student->id, 'lesson_id' => $lessonId],
            ['progress_percent' => $progressPercent]
        );

        return response()->json([
            'message' => 'تم حفظ التقدم',
            'progress_percent' => $progressPercent,
        ]);
    }

    public function complete(Request $request, int $lessonId): JsonResponse
    {
        $student = $this->student($request);
        $lesson = Lesson::query()->findOrFail($lessonId);

        $this->ensureLessonAccessible($student, $lesson);

        $progress = LessonProgress::query()
            ->where('student_id', $student->id)
            ->where('lesson_id', $lessonId)
            ->first();

        if ($lesson->video_url) {
            abort_unless(
                ($progress?->progress_percent ?? 0) >= 95,
                422,
                'يجب مشاهدة الفيديو كاملاً قبل إكمال الدرس'
            );
        }

        $lesson->loadCount('questions');

        if ($lesson->questions_count > 0) {
            abort_unless(
                $progress?->quiz_passed === true,
                422,
                'يجب اجتياز أسئلة الدرس قبل الإكمال'
            );
        }

        LessonProgress::query()->updateOrCreate(
            ['student_id' => $student->id, 'lesson_id' => $lessonId],
            ['progress_percent' => 100, 'is_completed' => true, 'completed_at' => now()]
        );

        return response()->json(['message' => 'تم إكمال الدرس']);
    }
}
