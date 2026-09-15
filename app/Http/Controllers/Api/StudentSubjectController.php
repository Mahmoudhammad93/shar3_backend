<?php

namespace App\Http\Controllers\Api;

use App\Actions\CalculateCourseProgressAction;
use App\Actions\ResolveStudentCurriculumAction;
use App\Http\Controllers\Api\Concerns\ResolvesAuthenticatedStudent;
use App\Http\Controllers\Controller;
use App\Models\Lesson;
use App\Models\LessonProgress;
use App\Models\Subject;
use App\Support\MediaUrl;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class StudentSubjectController extends Controller
{
    use ResolvesAuthenticatedStudent;

    public function __construct(
        private readonly ResolveStudentCurriculumAction $curriculum,
        private readonly CalculateCourseProgressAction $courseProgress,
    ) {}

    public function show(Request $request, Subject $subject): JsonResponse
    {
        $student = $this->student($request);

        abort_unless($this->subjectInCurriculum($student, $subject->id), 404, 'المادة غير متاحة في مقررك الحالي');

        $subject->load(['course.teacher', 'course.lessons' => fn ($query) => $query
            ->where('is_published', true)
            ->orderBy('sort_order')
            ->withCount('questions')]);

        abort_unless($subject->course_id && $subject->course, 404, 'محتوى هذه المادة قيد الإعداد');

        $course = $subject->course;
        $lessons = $course->lessons;

        $progressMap = LessonProgress::query()
            ->where('student_id', $student->id)
            ->whereIn('lesson_id', $lessons->pluck('id'))
            ->get()
            ->keyBy('lesson_id');

        $orderedLessons = $lessons->values();

        return response()->json([
            'subject' => [
                'id' => $subject->id,
                'name_ar' => $subject->name_ar,
                'slug' => $subject->slug,
                'primary_text_ar' => $subject->primary_text_ar,
                'supplementary_text_ar' => $subject->supplementary_text_ar,
                'memorization_ar' => $subject->memorization_ar,
            ],
            'course' => [
                'id' => $course->id,
                'title_ar' => $course->title_ar,
                'slug' => $course->slug,
                'image' => MediaUrl::resolve($course->image),
                'description_ar' => $course->description_ar,
                'teacher' => $course->teacher?->name_ar,
            ],
            'progress' => $this->courseProgress->forCourse($student, $course->id),
            'lessons' => $orderedLessons->map(function (Lesson $lesson, int $index) use ($orderedLessons, $progressMap) {
                $previousLesson = $index > 0 ? $orderedLessons[$index - 1] : null;
                $previousCompleted = $previousLesson
                    ? ($progressMap->get($previousLesson->id)?->is_completed ?? false)
                    : true;

                return [
                    'id' => $lesson->id,
                    'title_ar' => $lesson->title_ar,
                    'content_ar' => $lesson->content_ar,
                    'video_url' => $lesson->video_url,
                    'duration_minutes' => $lesson->duration_minutes,
                    'sort_order' => $lesson->sort_order,
                    'is_completed' => $progressMap->get($lesson->id)?->is_completed ?? false,
                    'progress_percent' => $progressMap->get($lesson->id)?->progress_percent ?? 0,
                    'quiz_passed' => $progressMap->get($lesson->id)?->quiz_passed ?? false,
                    'has_quiz' => ($lesson->questions_count ?? 0) > 0,
                    'is_locked' => ! $previousCompleted,
                ];
            }),
        ]);
    }

    private function subjectInCurriculum($student, int $subjectId): bool
    {
        return $this->curriculum->forStudent($student)->contains(
            fn (array $row) => ($row['subject_id'] ?? null) === $subjectId
        );
    }
}
