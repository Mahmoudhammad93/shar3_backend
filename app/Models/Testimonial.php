<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;

#[Fillable([
    'name_ar', 'name_en', 'role_ar', 'role_en', 'content_ar', 'content_en',
    'photo', 'rating', 'is_featured', 'is_active', 'sort_order',
])]
class Testimonial extends Model
{
    protected function casts(): array
    {
        return [
            'is_featured' => 'boolean',
            'is_active' => 'boolean',
            'rating' => 'integer',
            'sort_order' => 'integer',
        ];
    }
}
