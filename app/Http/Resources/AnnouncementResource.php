<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;

class AnnouncementResource extends BaseResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'title_ar' => $this->title_ar,
            'title_en' => $this->title_en,
            'slug' => $this->slug,
            'excerpt_ar' => $this->excerpt_ar,
            'excerpt_en' => $this->excerpt_en,
            'content_ar' => $this->content_ar,
            'content_en' => $this->content_en,
            'image' => $this->mediaUrl($this->image),
            'published_at' => $this->published_at?->format('Y-m-d'),
        ];
    }
}
