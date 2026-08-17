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
            'courses' => CourseResource::collection($this->whenLoaded('courses')),
        ];
    }
}
