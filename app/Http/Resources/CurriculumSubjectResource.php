<?php

namespace App\Http\Resources;

use App\Models\CurriculumAssignment;
use Illuminate\Http\Request;

/** @mixin CurriculumAssignment */
class CurriculumSubjectResource extends BaseResource
{
    public function toArray(Request $request): array
    {
        $subject = $this->subject;

        return [
            'id' => $subject?->id,
            'name_ar' => $subject?->name_ar,
            'name_en' => $subject?->name_en,
            'slug' => $subject?->slug,
            'is_required' => $this->is_required,
            'memorization_ar' => $subject?->memorization_ar,
            'memorization_en' => $subject?->memorization_en,
            'primary_text_ar' => $subject?->primary_text_ar,
            'primary_text_en' => $subject?->primary_text_en,
            'supplementary_text_ar' => $subject?->supplementary_text_ar,
            'supplementary_text_en' => $subject?->supplementary_text_en,
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
