<?php

namespace App\Support;

use App\Models\AcademicLevel;
use App\Models\AcademicYear;
use App\Models\CurriculumAssignment;
use App\Models\Program;
use App\Models\Semester;
use Illuminate\Support\Collection;

class ProgramCurriculumSubjects
{
    /** @var array<string, string> */
    private const PROGRAM_LEVEL_MAP = [
        'preparatory-program' => 'preparatory-level',
        'taaseel-program' => 'taaseel-level',
        'specialization-program' => 'specialized-level',
    ];

    /**
     * @return array{
     *     years: Collection<int, array{
     *         id: int,
     *         name_ar: string,
     *         name_en: ?string,
     *         slug: string,
     *         year_number: int,
     *         semesters: Collection<int, array{
     *             id: int,
     *             name_ar: string,
     *             name_en: ?string,
     *             slug: string,
     *             semester_number: int,
     *             subjects: Collection<int, CurriculumAssignment>
     *         }>
     *     }>,
     *     subjects: Collection<int, CurriculumAssignment>,
     *     specializations: Collection<int, array{
     *         id: int,
     *         name_ar: string,
     *         name_en: ?string,
     *         slug: string,
     *         description_ar: ?string,
     *         years: Collection<int, array{
     *             id: int,
     *             name_ar: string,
     *             name_en: ?string,
     *             slug: string,
     *             year_number: int,
     *             semesters: Collection<int, array{
     *                 id: int,
     *                 name_ar: string,
     *                 name_en: ?string,
     *                 slug: string,
     *                 semester_number: int,
     *                 subjects: Collection<int, CurriculumAssignment>
     *             }>
     *         }>,
     *         subjects: Collection<int, CurriculumAssignment>
     *     }>
     * }
     */
    public function forProgram(Program $program): array
    {
        $levelSlug = self::PROGRAM_LEVEL_MAP[$program->slug] ?? null;

        if ($levelSlug === null) {
            return ['years' => collect(), 'subjects' => collect(), 'specializations' => collect()];
        }

        $level = AcademicLevel::query()
            ->where('slug', $levelSlug)
            ->where('is_active', true)
            ->first();

        if ($level === null) {
            return ['years' => collect(), 'subjects' => collect(), 'specializations' => collect()];
        }

        if ($level->isSpecialized()) {
            return $this->forSpecializedLevel($level);
        }

        return $this->forGeneralLevel($level);
    }

    /** @return array{years: Collection, subjects: Collection, specializations: Collection} */
    private function forGeneralLevel(AcademicLevel $level): array
    {
        $level->load([
            'years' => fn ($q) => $q->where('is_active', true)->orderBy('sort_order'),
            'years.semesters' => fn ($q) => $q->where('is_active', true)->orderBy('sort_order'),
            'years.semesters.curriculumAssignments' => fn ($q) => $q
                ->where('is_active', true)
                ->whereNull('specialization_id')
                ->orderBy('sort_order')
                ->with(['subject.course:id,slug,title_ar,title_en']),
        ]);

        $years = $this->mapYearsFromLoaded($level->years);

        return [
            'years' => $years,
            'subjects' => $this->flattenSubjectsFromYears($years),
            'specializations' => collect(),
        ];
    }

    /** @return array{years: Collection, subjects: Collection, specializations: Collection} */
    private function forSpecializedLevel(AcademicLevel $level): array
    {
        $level->load([
            'specializations' => fn ($q) => $q->where('is_active', true)->orderBy('sort_order'),
            'specializations.curriculumAssignments' => fn ($q) => $q
                ->where('is_active', true)
                ->orderBy('sort_order')
                ->with([
                    'subject.course:id,slug,title_ar,title_en',
                    'semester.year',
                ]),
        ]);

        $specializations = $level->specializations->map(function ($specialization) {
            $years = $this->mapYearsFromAssignments($specialization->curriculumAssignments);
            $subjects = $this->flattenSubjectsFromYears($years);

            return [
                'id' => $specialization->id,
                'name_ar' => $specialization->name_ar,
                'name_en' => $specialization->name_en,
                'slug' => $specialization->slug,
                'description_ar' => $specialization->description_ar,
                'years' => $years,
                'subjects' => $subjects,
            ];
        })->values();

        return [
            'years' => collect(),
            'subjects' => collect(),
            'specializations' => $specializations,
        ];
    }

    /**
     * @param  Collection<int, AcademicYear>  $years
     * @return Collection<int, array<string, mixed>>
     */
    private function mapYearsFromLoaded(Collection $years): Collection
    {
        return $years
            ->map(function (AcademicYear $year) {
                $semesters = $year->semesters
                    ->map(function (Semester $semester) {
                        $subjects = $semester->curriculumAssignments->values();

                        if ($subjects->isEmpty()) {
                            return null;
                        }

                        return [
                            'id' => $semester->id,
                            'name_ar' => $semester->name_ar,
                            'name_en' => $semester->name_en,
                            'slug' => $semester->slug,
                            'semester_number' => $semester->semester_number,
                            'subjects' => $subjects,
                        ];
                    })
                    ->filter()
                    ->values();

                if ($semesters->isEmpty()) {
                    return null;
                }

                return [
                    'id' => $year->id,
                    'name_ar' => $year->name_ar,
                    'name_en' => $year->name_en,
                    'slug' => $year->slug,
                    'year_number' => $year->year_number,
                    'semesters' => $semesters,
                ];
            })
            ->filter()
            ->values();
    }

    /**
     * @param  Collection<int, CurriculumAssignment>  $assignments
     * @return Collection<int, array<string, mixed>>
     */
    private function mapYearsFromAssignments(Collection $assignments): Collection
    {
        return $assignments
            ->filter(fn (CurriculumAssignment $assignment) => $assignment->semester?->year !== null)
            ->groupBy(fn (CurriculumAssignment $assignment) => $assignment->semester->year->id)
            ->sortKeys()
            ->map(function (Collection $yearAssignments) {
                /** @var AcademicYear $year */
                $year = $yearAssignments->first()->semester->year;

                $semesters = $yearAssignments
                    ->groupBy(fn (CurriculumAssignment $assignment) => $assignment->semester_id)
                    ->sortKeys()
                    ->map(function (Collection $semesterAssignments) {
                        /** @var Semester $semester */
                        $semester = $semesterAssignments->first()->semester;

                        return [
                            'id' => $semester->id,
                            'name_ar' => $semester->name_ar,
                            'name_en' => $semester->name_en,
                            'slug' => $semester->slug,
                            'semester_number' => $semester->semester_number,
                            'subjects' => $semesterAssignments->sortBy('sort_order')->values(),
                        ];
                    })
                    ->values();

                return [
                    'id' => $year->id,
                    'name_ar' => $year->name_ar,
                    'name_en' => $year->name_en,
                    'slug' => $year->slug,
                    'year_number' => $year->year_number,
                    'semesters' => $semesters,
                ];
            })
            ->sortBy('year_number')
            ->values();
    }

    /** @param  Collection<int, array<string, mixed>>  $years */
    private function flattenSubjectsFromYears(Collection $years): Collection
    {
        return $years
            ->flatMap(fn (array $year) => $year['semesters'])
            ->flatMap(fn (array $semester) => $semester['subjects'])
            ->values();
    }
}
