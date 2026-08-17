<?php

namespace App\Http\Controllers\Api\Concerns;

use App\Models\Enrollment;
use App\Models\Lesson;
use App\Models\Student;
use Illuminate\Http\Request;

trait ResolvesAuthenticatedStudent
{
    protected function student(Request $request): Student
    {
        $user = $request->user();

        abort_unless($user && $user->role === 'student', 403, 'غير مصرح');

        $student = $user->student;

        abort_unless($student, 403, 'Student profile not found');

        abort_if($student->status === Student::STATUS_SUSPENDED, 403, 'تم إيقاف حسابك. يرجى التواصل مع الإدارة.');

        return $student;
    }

    protected function ensureLessonAccessible(Student $student, Lesson $lesson): void
    {
        $enrolled = Enrollment::query()
            ->where('student_id', $student->id)
            ->where('course_id', $lesson->course_id)
            ->whereIn('status', [Enrollment::STATUS_APPROVED, Enrollment::STATUS_COMPLETED])
            ->exists();

        abort_unless($enrolled, 403);

        $previousIncomplete = Lesson::query()
            ->where('course_id', $lesson->course_id)
            ->where('is_published', true)
            ->where('sort_order', '<', $lesson->sort_order)
            ->whereDoesntHave('progress', fn ($query) => $query
                ->where('student_id', $student->id)
                ->where('is_completed', true))
            ->exists();

        abort_if($previousIncomplete, 403, 'يجب إكمال الدرس السابق أولاً');
    }
}
