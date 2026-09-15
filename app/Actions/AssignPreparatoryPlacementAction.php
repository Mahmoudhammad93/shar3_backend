<?php

namespace App\Actions;

use App\Models\AcademicLevel;
use App\Models\AcademicYear;

class AssignPreparatoryPlacementAction
{
    public function __construct(
        private readonly DetermineCurrentSemesterAction $determineCurrentSemester,
    ) {}

    /** @return array{academic_level_id: int, academic_year_id: int, current_semester_id: int} */
    public function execute(): array
    {
        $level = AcademicLevel::query()
            ->where('slug', 'preparatory-level')
            ->where('is_active', true)
            ->firstOrFail();

        $year = AcademicYear::query()
            ->where('slug', 'first-year')
            ->where('academic_level_id', $level->id)
            ->where('is_active', true)
            ->firstOrFail();

        $semester = $this->determineCurrentSemester->forYear($year);

        return [
            'academic_level_id' => $level->id,
            'academic_year_id' => $year->id,
            'current_semester_id' => $semester->id,
        ];
    }
}
