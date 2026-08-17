<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;

class HeroSlideResource extends BaseResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'title_ar' => $this->title_ar,
            'title_en' => $this->title_en,
            'subtitle_ar' => $this->subtitle_ar,
            'subtitle_en' => $this->subtitle_en,
            'button_text_ar' => $this->button_text_ar,
            'button_text_en' => $this->button_text_en,
            'button_url' => $this->button_url,
            'image' => $this->mediaUrl($this->image),
        ];
    }
}
