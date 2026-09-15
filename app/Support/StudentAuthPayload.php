<?php

namespace App\Support;

use App\Models\Student;

class StudentAuthPayload
{
    /** @return array<string, mixed>|null */
    public static function for(Student $student): ?array
    {
        $student->loadMissing([
            'academicLevel',
            'academicYear',
            'currentSemester',
        ]);

        return [
            'id' => $student->id,
            'status' => $student->statusKey(),
            'status_label' => $student->statusLabel(),
            'rejection_reason' => $student->status === Student::STATUS_REJECTED
                ? $student->rejection_reason
                : null,
            'academic_level' => $student->academicLevel ? [
                'id' => $student->academicLevel->id,
                'name_ar' => $student->academicLevel->name_ar,
                'slug' => $student->academicLevel->slug,
            ] : null,
            'academic_year' => $student->academicYear ? [
                'id' => $student->academicYear->id,
                'name_ar' => $student->academicYear->name_ar,
                'slug' => $student->academicYear->slug,
                'year_number' => $student->academicYear->year_number,
            ] : null,
            'current_semester' => $student->currentSemester ? [
                'id' => $student->currentSemester->id,
                'name_ar' => $student->currentSemester->name_ar,
                'semester_number' => $student->currentSemester->semester_number,
            ] : null,
        ];
    }
}
