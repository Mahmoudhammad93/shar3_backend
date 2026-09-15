<?php

namespace App\Http\Controllers\Api;

use App\Actions\CalculateCourseProgressAction;
use App\Http\Controllers\Api\Concerns\ResolvesAuthenticatedStudent;
use App\Http\Controllers\Controller;
use App\Models\Enrollment;
use App\Models\Lesson;
use App\Models\LessonProgress;
use App\Support\LessonMedia;
use App\Support\MediaUrl;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class StudentCourseController extends Controller
{
    use ResolvesAuthenticatedStudent;

    public function __construct(private readonly CalculateCourseProgressAction $courseProgress) {}

    public function index(Request $request): JsonResponse
    {
        $student = $this->student($request);

        $enrollments = Enrollment::query()
            ->with(['course.teacher', 'course.lessons', 'course.category'])
            ->where('student_id', $student->id)
            ->whereIn('status', [
                Enrollment::STATUS_PENDING,
                Enrollment::STATUS_APPROVED,
                Enrollment::STATUS_COMPLETED,
            ])
            ->orderByRaw("CASE status WHEN 'approved' THEN 1 WHEN 'completed' THEN 2 WHEN 'pending' THEN 3 WHEN 'rejected' THEN 4 ELSE 5 END")
            ->get();

        $progressMap = $this->courseProgress->forCourses(
            $student,
            $enrollments
                ->filter(fn (Enrollment $enrollment) => $enrollment->isAccessible())
                ->pluck('course_id')
                ->all(),
        );

        return response()->json([
            'data' => $enrollments->map(function (Enrollment $enrollment) use ($progressMap) {
                $lessonsCount = $enrollment->course?->lessons->count() ?? 0;
                $progress = $enrollment->isAccessible()
                    ? ($progressMap[$enrollment->course_id] ?? 0)
                    : 0;
                $completedLessons = $lessonsCount > 0
                    ? (int) round($progress * $lessonsCount / 100)
                    : 0;

                return [
                    'enrollment_id' => $enrollment->id,
                    'status' => $enrollment->status,
                    'course' => [
                        'id' => $enrollment->course?->id,
                        'title_ar' => $enrollment->course?->title_ar,
                        'slug' => $enrollment->course?->slug,
                        'image' => MediaUrl::resolve($enrollment->course?->image),
                        'description_ar' => $enrollment->course?->description_ar,
                        'category' => $enrollment->course?->category?->name_ar ?? $enrollment->course?->category?->name_en,
                        'teacher' => $enrollment->course?->teacher?->name_ar,
                        'lessons_count' => $lessonsCount,
                        'completed_lessons' => $completedLessons,
                    ],
                    'progress' => $progress,
                ];
            }),
        ]);
    }

    public function show(Request $request, int $courseId): JsonResponse
    {
        $student = $this->student($request);

        $enrollment = Enrollment::query()
            ->with([
                'course.teacher',
                'course.lessons' => fn ($query) => $query
                    ->where('is_published', true)
                    ->orderBy('sort_order')
                    ->withCount('questions'),
            ])
            ->where('student_id', $student->id)
            ->where('course_id', $courseId)
            ->whereIn('status', [Enrollment::STATUS_APPROVED, Enrollment::STATUS_COMPLETED])
            ->firstOrFail();

        $lessons = $enrollment->course->lessons;

        $progressMap = LessonProgress::query()
            ->where('student_id', $student->id)
            ->whereIn('lesson_id', $lessons->pluck('id'))
            ->get()
            ->keyBy('lesson_id');

        $orderedLessons = $lessons->values();

        return response()->json([
            'course' => [
                'id' => $enrollment->course->id,
                'title_ar' => $enrollment->course->title_ar,
                'slug' => $enrollment->course->slug,
                'image' => MediaUrl::resolve($enrollment->course->image),
                'description_ar' => $enrollment->course->description_ar,
                'teacher' => $enrollment->course->teacher?->name_ar,
            ],
            'progress' => $this->courseProgress->forCourse($student, $courseId),
            'lessons' => $orderedLessons->map(function (Lesson $lesson, int $index) use ($orderedLessons, $progressMap) {
                $previousLesson = $index > 0 ? $orderedLessons[$index - 1] : null;
                $previousCompleted = $previousLesson
                    ? ($progressMap->get($previousLesson->id)?->is_completed ?? false)
                    : true;

                $media = LessonMedia::playbackPayload($lesson);

                return array_merge([
                    'id' => $lesson->id,
                    'title_ar' => $lesson->title_ar,
                    'content_ar' => $lesson->content_ar,
                    'video_url' => LessonMedia::legacyOrNullVideoUrl($lesson),
                    'duration_minutes' => $lesson->duration_minutes,
                    'sort_order' => $lesson->sort_order,
                    'is_completed' => $progressMap->get($lesson->id)?->is_completed ?? false,
                    'progress_percent' => $progressMap->get($lesson->id)?->progress_percent ?? 0,
                    'quiz_passed' => $progressMap->get($lesson->id)?->quiz_passed ?? false,
                    'has_quiz' => ($lesson->questions_count ?? 0) > 0,
                    'is_locked' => ! $previousCompleted,
                ], $media);
            }),
        ]);
    }
}
