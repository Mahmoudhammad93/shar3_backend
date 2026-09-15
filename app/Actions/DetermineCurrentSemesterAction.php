<?php

namespace App\Actions;

use App\Models\AcademicYear;
use App\Models\Semester;

class DetermineCurrentSemesterAction
{
    public function forYear(AcademicYear $year): Semester
    {
        $semesters = Semester::query()
            ->where('academic_year_id', $year->id)
            ->where('is_active', true)
            ->orderBy('semester_number')
            ->get();

        if ($semesters->isEmpty()) {
            throw new \RuntimeException("No active semesters found for academic year {$year->id}.");
        }

        $today = now()->startOfDay();

        foreach ($semesters as $semester) {
            if ($semester->starts_at && $semester->ends_at
                && $today->between($semester->starts_at->startOfDay(), $semester->ends_at->startOfDay())) {
                return $semester;
            }
        }

        $datedSemesters = $semesters->filter(
            fn (Semester $semester) => $semester->starts_at && $semester->ends_at
        );

        if ($datedSemesters->isNotEmpty()) {
            $lastDated = $datedSemesters->last();

            if ($lastDated->ends_at && $today->gt($lastDated->ends_at->startOfDay())) {
                return $lastDated;
            }
        }

        return $semesters->first();
    }
}
