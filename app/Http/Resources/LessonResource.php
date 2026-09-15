<?php

namespace App\Http\Resources;

use App\Support\LessonMedia;
use Illuminate\Http\Request;

class LessonResource extends BaseResource
{
    public function toArray(Request $request): array
    {
        $media = LessonMedia::playbackPayload($this->resource);

        return [
            'id' => $this->id,
            'title_ar' => $this->title_ar,
            'title_en' => $this->title_en,
            'content_ar' => $this->content_ar,
            'content_en' => $this->content_en,
            'video_url' => LessonMedia::legacyOrNullVideoUrl($this->resource),
            'duration_minutes' => $this->duration_minutes,
            'sort_order' => $this->sort_order,
            'media_type' => $media['media_type'],
            'video_provider' => $media['video_provider'] ?? null,
            'bunny' => $media['bunny'] ?? null,
            'audio' => $media['audio'] ?? null,
        ];
    }
}
