<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\FaqResource;
use App\Models\Faq;
use Illuminate\Http\JsonResponse;

class FaqController extends Controller
{
    public function index(): JsonResponse
    {
        $faqs = Faq::query()->where('is_active', true)->orderBy('sort_order')->get();

        return response()->json(['data' => FaqResource::collection($faqs)]);
    }
}
