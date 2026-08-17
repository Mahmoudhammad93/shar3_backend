<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;

class LessonResource extends BaseResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'title_ar' => $this->title_ar,
            'title_en' => $this->title_en,
            'content_ar' => $this->content_ar,
            'content_en' => $this->content_en,
            'video_url' => $this->video_url,
            'duration_minutes' => $this->duration_minutes,
            'sort_order' => $this->sort_order,
        ];
    }
}
