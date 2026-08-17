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
            'years' => $this->when(! $isSpecialized && $this->relationLoaded('years'), fn () => $this->years->map(fn ($year) => [
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
            ])),
            'specializations' => $this->when($isSpecialized && $this->relationLoaded('specializations'), fn () => $this->specializations->map(fn ($spec) => [
                'id' => $spec->id,
                'name_ar' => $spec->name_ar,
                'name_en' => $spec->name_en,
                'slug' => $spec->slug,
                'description_ar' => $spec->description_ar,
                'years' => $this->specializationYearsPayload($spec),
            ])),
        ];
    }

    /** @return array<int, array<string, mixed>> */
    private function specializationYearsPayload($specialization): array
    {
        if (! $specialization->relationLoaded('curriculumAssignments')) {
            return [];
        }

        return $specialization->curriculumAssignments
            ->groupBy(fn (CurriculumAssignment $a) => $a->semester?->year_id)
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
        $subject = $assignment->subject;

        return [
            'id' => $subject?->id,
            'name_ar' => $subject?->name_ar,
            'name_en' => $subject?->name_en,
            'slug' => $subject?->slug,
            'is_required' => $assignment->is_required,
            'memorization_ar' => $subject?->memorization_ar,
            'memorization_en' => $subject?->memorization_en,
            'primary_text_ar' => $subject?->primary_text_ar,
            'primary_text_en' => $subject?->primary_text_en,
            'supplementary_text_ar' => $subject?->supplementary_text_ar,
            'supplementary_text_en' => $subject?->supplementary_text_en,
            'specialization_id' => $assignment->specialization_id,
            'course' => $subject?->relationLoaded('course') && $subject?->course
                ? [
                    'id' => $subject->course->id,
                    'slug' => $subject->course->slug,
                    'title_ar' => $subject->course->title_ar,
                    'title_en' => $subject->course->title_en,
                ]
                : null,
        ];
    }
}
