<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;

class TeacherResource extends BaseResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name_ar' => $this->name_ar,
            'name_en' => $this->name_en,
            'slug' => $this->slug,
            'title_ar' => $this->title_ar,
            'title_en' => $this->title_en,
            'bio_ar' => $this->bio_ar,
            'bio_en' => $this->bio_en,
            'specializations' => $this->specializations,
            'photo' => $this->mediaUrl($this->photo),
            'is_featured' => $this->is_featured,
            'courses_count' => $this->whenCounted('courses'),
            'courses' => CourseResource::collection($this->whenLoaded('courses')),
        ];
    }
}
