<?php

namespace App\Actions;

use App\Models\Enrollment;
use App\Models\Student;
use App\Models\Subject;

class EnsureStudentCanAccessCourseAction
{
    public function __construct(
        private readonly ResolveStudentCurriculumAction $curriculum,
    ) {}

    public function execute(Student $student, int $courseId): bool
    {
        $enrolled = Enrollment::query()
            ->where('student_id', $student->id)
            ->where('course_id', $courseId)
            ->whereIn('status', [Enrollment::STATUS_APPROVED, Enrollment::STATUS_COMPLETED])
            ->exists();

        if ($enrolled) {
            return true;
        }

        $curriculumSubjectIds = $this->curriculum->forStudent($student)->pluck('subject_id')->filter();

        if ($curriculumSubjectIds->isEmpty()) {
            return false;
        }

        return Subject::query()
            ->where('course_id', $courseId)
            ->whereIn('id', $curriculumSubjectIds)
            ->exists();
    }
}
