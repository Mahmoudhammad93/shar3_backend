<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;

class CourseResource extends BaseResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'title_ar' => $this->title_ar,
            'title_en' => $this->title_en,
            'slug' => $this->slug,
            'description_ar' => $this->description_ar,
            'description_en' => $this->description_en,
            'image' => $this->mediaUrl($this->image),
            'duration_hours' => $this->duration_hours,
            'level' => $this->level,
            'price' => $this->price,
            'is_free' => $this->is_free,
            'is_featured' => $this->is_featured,
            'start_date' => $this->start_date?->format('Y-m-d'),
            'end_date' => $this->end_date?->format('Y-m-d'),
            'category' => $this->whenLoaded('category', fn () => [
                'name_ar' => $this->category?->name_ar,
                'slug' => $this->category?->slug,
            ]),
            'program' => $this->whenLoaded('program', fn () => [
                'name_ar' => $this->program?->name_ar,
                'slug' => $this->program?->slug,
            ]),
            'teacher' => $this->whenLoaded('teacher', fn () => [
                'name_ar' => $this->teacher?->name_ar,
                'slug' => $this->teacher?->slug,
                'photo' => $this->mediaUrl($this->teacher?->photo),
            ]),
            'lessons' => LessonResource::collection($this->whenLoaded('lessons')),
        ];
    }
}
