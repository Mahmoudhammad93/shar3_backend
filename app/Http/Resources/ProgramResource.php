<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;

class ProgramResource extends BaseResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name_ar' => $this->name_ar,
            'name_en' => $this->name_en,
            'slug' => $this->slug,
            'description_ar' => $this->description_ar,
            'description_en' => $this->description_en,
            'duration' => $this->duration,
            'level' => $this->level,
            'image' => $this->mediaUrl($this->image),
            'courses_count' => $this->whenCounted('courses'),
            'subjects_count' => $this->when(
                isset($this->curriculum_subjects) || isset($this->curriculum_specializations),
                fn () => ($this->curriculum_subjects?->count() ?? 0)
                    + ($this->curriculum_specializations?->sum(fn ($specialization) => $specialization['subjects']->count()) ?? 0),
            ),
            'years' => $this->when(
                isset($this->curriculum_years) && $this->curriculum_years->isNotEmpty(),
                fn () => ProgramCurriculumYearResource::collection($this->curriculum_years),
            ),
            'subjects' => $this->when(
                isset($this->curriculum_subjects),
                fn () => CurriculumSubjectResource::collection($this->curriculum_subjects),
            ),
            'specializations' => $this->when(
                isset($this->curriculum_specializations) && $this->curriculum_specializations->isNotEmpty(),
                fn () => $this->curriculum_specializations->map(fn ($specialization) => [
                    'id' => $specialization['id'],
                    'name_ar' => $specialization['name_ar'],
                    'name_en' => $specialization['name_en'],
                    'slug' => $specialization['slug'],
                    'description_ar' => $specialization['description_ar'],
                    'years' => ProgramCurriculumYearResource::collection($specialization['years']),
                    'subjects' => CurriculumSubjectResource::collection($specialization['subjects']),
                ])->values()->all(),
            ),
        ];
    }
}
