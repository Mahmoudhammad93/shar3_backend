<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;

class AcademicStructureResource extends BaseResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name_ar' => $this->name_ar,
            'name_en' => $this->name_en,
            'slug' => $this->slug,
            'number' => $this->number,
            'description_ar' => $this->description_ar,
            'years' => $this->whenLoaded('years', fn () => $this->years->map(fn ($year) => [
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
                        'subjects' => $semester->relationLoaded('subjects')
                            ? $semester->subjects->map(fn ($subject) => [
                                'id' => $subject->id,
                                'name_ar' => $subject->name_ar,
                                'name_en' => $subject->name_en,
                                'slug' => $subject->slug,
                                'is_required' => $subject->is_required,
                                'memorization_ar' => $subject->memorization_ar,
                                'memorization_en' => $subject->memorization_en,
                                'primary_text_ar' => $subject->primary_text_ar,
                                'primary_text_en' => $subject->primary_text_en,
                                'supplementary_text_ar' => $subject->supplementary_text_ar,
                                'supplementary_text_en' => $subject->supplementary_text_en,
                                'specialization_id' => $subject->specialization_id,
                                'course' => $subject->relationLoaded('course') && $subject->course
                                    ? [
                                        'id' => $subject->course->id,
                                        'slug' => $subject->course->slug,
                                        'title_ar' => $subject->course->title_ar,
                                        'title_en' => $subject->course->title_en,
                                    ]
                                    : null,
                            ])
                            : [],
                    ])
                    : [],
            ])),
            'specializations' => $this->whenLoaded('specializations', fn () => $this->specializations->map(fn ($spec) => [
                'id' => $spec->id,
                'name_ar' => $spec->name_ar,
                'name_en' => $spec->name_en,
                'slug' => $spec->slug,
                'description_ar' => $spec->description_ar,
            ])),
        ];
    }
}
