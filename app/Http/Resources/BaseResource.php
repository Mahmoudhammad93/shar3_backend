<?php

namespace App\Http\Resources;

use App\Support\MediaUrl;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

abstract class BaseResource extends JsonResource
{
    protected function mediaUrl(?string $path): ?string
    {
        return MediaUrl::resolve($path);
    }
}
