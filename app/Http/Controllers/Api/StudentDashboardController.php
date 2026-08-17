<?php

namespace App\Http\Controllers\Api;

use App\Actions\CalculateCourseProgressAction;
use App\Http\Controllers\Api\Concerns\ResolvesAuthenticatedStudent;
use App\Http\Controllers\Controller;
use App\Models\Assignment;
use App\Models\Certificate;
use App\Models\Enrollment;
use App\Models\LessonProgress;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class StudentDashboardController extends Controller
{
    use ResolvesAuthenticatedStudent;

    public function __construct(private readonly CalculateCourseProgressAction $courseProgress) {}

    public function __invoke(Request $request): JsonResponse
    {
        $student = $this->student($request);

        $enrollments = Enrollment::query()
            ->with(['course.lessons'])
            ->where('student_id', $student->id)
            ->whereIn('status', [Enrollment::STATUS_APPROVED, Enrollment::STATUS_COMPLETED])
            ->get();

        $pendingEnrollments = Enrollment::query()
            ->where('student_id', $student->id)
            ->where('status', Enrollment::STATUS_PENDING)
            ->count();

        $lessonIds = $enrollments
            ->flatMap(fn (Enrollment $enrollment) => $enrollment->course?->lessons?->pluck('id') ?? collect())
            ->unique()
            ->values();

        $totalLessons = $lessonIds->count();
        $completedLessons = $lessonIds->isEmpty()
            ? 0
            : LessonProgress::query()
                ->where('student_id', $student->id)
                ->whereIn('lesson_id', $lessonIds)
                ->where('is_completed', true)
                ->count();

        $progressPercent = $totalLessons > 0 ? round(($completedLessons / $totalLessons) * 100) : 0;

        $pendingAssignments = Assignment::query()
            ->whereIn('course_id', $enrollments->pluck('course_id'))
            ->where('is_published', true)
            ->count();

        $progressMap = $this->courseProgress->forCourses(
            $student,
            $enrollments->pluck('course_id')->filter()->all(),
        );

        return response()->json([
            'stats' => [
                'active_courses' => $enrollments->count(),
                'pending_enrollments' => $pendingEnrollments,
                'completed_lessons' => $completedLessons,
                'total_lessons' => $totalLessons,
                'progress_percent' => $progressPercent,
                'pending_assignments' => $pendingAssignments,
                'certificates' => Certificate::query()->where('student_id', $student->id)->count(),
            ],
            'recent_courses' => $enrollments->take(3)->map(fn (Enrollment $enrollment) => [
                'id' => $enrollment->course?->id,
                'title_ar' => $enrollment->course?->title_ar,
                'slug' => $enrollment->course?->slug,
                'progress' => $progressMap[$enrollment->course_id] ?? 0,
            ]),
        ]);
    }
}
