<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;

class TestimonialResource extends BaseResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name_ar' => $this->name_ar,
            'name_en' => $this->name_en,
            'role_ar' => $this->role_ar,
            'role_en' => $this->role_en,
            'content_ar' => $this->content_ar,
            'content_en' => $this->content_en,
            'photo' => $this->mediaUrl($this->photo),
            'rating' => $this->rating,
        ];
    }
}
