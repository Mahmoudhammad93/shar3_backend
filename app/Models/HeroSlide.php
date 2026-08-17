<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;

#[Fillable([
    'title_ar', 'title_en', 'subtitle_ar', 'subtitle_en',
    'button_text_ar', 'button_text_en', 'button_url', 'image', 'sort_order', 'is_active',
])]
class HeroSlide extends Model
{
    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
            'sort_order' => 'integer',
        ];
    }
}
