<?php

namespace App\Http\Resources;

use App\Enums\CurriculumType;
use App\Models\CurriculumAssignment;
use Illuminate\Http\Request;

class AcademicStructureResource extends BaseResource
{
    public function toArray(Request $request): array
    {
        $isSpecialized = $this->curriculum_type === CurriculumType::Specialized;

        return [
            'id' => $this->id,
            'name_ar' => $this->name_ar,
            'name_en' => $this->name_en,
            'slug' => $this->slug,
            'number' => $this->number,
            'curriculum_type' => $this->curriculum_type?->value,
            'description_ar' => $this->description_ar,
            'years' => $isSpecialized
                ? []
                : ($this->relationLoaded('years')
                    ? $this->years->map(fn ($year) => [
                        'id' => $year->id,
                        'name_ar' => $year->name_ar,
                        'name_en' => $year->name_en,
                        'slug' => $year->slug,
                        'year_number' => $year->year_number,
                        'semesters' => $year->relationLoaded('semesters')
                            ? $year->semesters->map(fn ($semester) => [
                                'id' => $semester->id,
                                'name_ar' => $semester->name_ar,
                                'name_en' => $semester->name_en,
                                'slug' => $semester->slug,
                                'semester_number' => $semester->semester_number,
                                'starts_at' => $semester->starts_at?->format('Y-m-d'),
                                'ends_at' => $semester->ends_at?->format('Y-m-d'),
                                'subjects' => $semester->relationLoaded('curriculumAssignments')
                                    ? $semester->curriculumAssignments->map(fn ($assignment) => $this->subjectPayload($assignment))
                                    : [],
                            ])
                            : [],
                    ])->values()->all()
                    : []),
            'specializations' => $isSpecialized && $this->relationLoaded('specializations')
                ? $this->specializations->map(fn ($spec) => [
                    'id' => $spec->id,
                    'name_ar' => $spec->name_ar,
                    'name_en' => $spec->name_en,
                    'slug' => $spec->slug,
                    'description_ar' => $spec->description_ar,
                    'years' => $this->specializationYearsPayload($spec),
                ])->values()->all()
                : [],
        ];
    }

    /** @return array<int, array<string, mixed>> */
    private function specializationYearsPayload($specialization): array
    {
        if (! $specialization->relationLoaded('curriculumAssignments')) {
            return [];
        }

        return $specialization->curriculumAssignments
            // Semester belongs to AcademicYear via academic_year_id (not year_id).
            // Skip malformed rows with missing semester/year so grouping never throws.
            ->filter(fn (CurriculumAssignment $a) => filled($a->semester?->academic_year_id))
            ->groupBy(fn (CurriculumAssignment $a) => $a->semester->academic_year_id)
            ->map(function ($assignments, $yearId) {
                $year = $assignments->first()?->semester?->year;

                return [
                    'id' => $year?->id ?? $yearId,
                    'name_ar' => $year?->name_ar,
                    'name_en' => $year?->name_en,
                    'slug' => $year?->slug,
                    'year_number' => $year?->year_number,
                    'semesters' => $assignments
                        ->groupBy('semester_id')
                        ->map(function ($semesterAssignments) {
                            $semester = $semesterAssignments->first()?->semester;

                            return [
                                'id' => $semester?->id,
                                'name_ar' => $semester?->name_ar,
                                'name_en' => $semester?->name_en,
                                'slug' => $semester?->slug,
                                'semester_number' => $semester?->semester_number,
                                'subjects' => $semesterAssignments
                                    ->sortBy('sort_order')
                                    ->map(fn ($assignment) => $this->subjectPayload($assignment))
                                    ->values()
                                    ->all(),
                            ];
                        })
                        ->sortBy('semester_number')
                        ->values()
                        ->all(),
                ];
            })
            ->sortBy('year_number')
            ->values()
            ->all();
    }

    /** @return array<string, mixed> */
    private function subjectPayload(CurriculumAssignment $assignment): array
    {
        return CurriculumSubjectResource::make($assignment)->resolve();
    }
}
