<?php

namespace App\Actions;

use App\Models\Lesson;
use App\Models\LessonProgress;
use App\Models\Student;
use Illuminate\Support\Collection;

class CalculateCourseProgressAction
{
    public function forCourse(Student $student, ?int $courseId): int
    {
        if (! $courseId) {
            return 0;
        }

        return $this->forCourses($student, collect([$courseId]))[$courseId] ?? 0;
    }

    /**
     * @param  Collection<int, int>|array<int, int>  $courseIds
     * @return array<int, int>
     */
    public function forCourses(Student $student, Collection|array $courseIds): array
    {
        $courseIds = collect($courseIds)->filter()->unique()->values();

        if ($courseIds->isEmpty()) {
            return [];
        }

        $lessonCounts = Lesson::query()
            ->selectRaw('course_id, COUNT(*) as total')
            ->whereIn('course_id', $courseIds)
            ->groupBy('course_id')
            ->pluck('total', 'course_id');

        $completedCounts = LessonProgress::query()
            ->selectRaw('lessons.course_id, COUNT(*) as completed')
            ->join('lessons', 'lessons.id', '=', 'lesson_progress.lesson_id')
            ->where('lesson_progress.student_id', $student->id)
            ->where('lesson_progress.is_completed', true)
            ->whereIn('lessons.course_id', $courseIds)
            ->groupBy('lessons.course_id')
            ->pluck('completed', 'course_id');

        $progress = [];

        foreach ($courseIds as $courseId) {
            $total = (int) ($lessonCounts[$courseId] ?? 0);

            if ($total === 0) {
                $progress[$courseId] = 0;

                continue;
            }

            $completed = (int) ($completedCounts[$courseId] ?? 0);
            $progress[$courseId] = (int) round(($completed / $total) * 100);
        }

        return $progress;
    }
}
