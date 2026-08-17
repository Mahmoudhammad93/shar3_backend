<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\AcademicStructureResource;
use App\Models\AcademicLevel;
use Illuminate\Http\JsonResponse;

class AcademicController extends Controller
{
    public function structure(): JsonResponse
    {
        $levels = AcademicLevel::query()
            ->where('is_active', true)
            ->with([
                'years' => fn ($q) => $q->where('is_active', true)->orderBy('sort_order'),
                'years.semesters' => fn ($q) => $q->where('is_active', true)->orderBy('sort_order'),
                'years.semesters.subjects' => fn ($q) => $q->where('is_active', true)->orderBy('sort_order')->with('course:id,slug,title_ar,title_en'),
                'specializations' => fn ($q) => $q->where('is_active', true)->orderBy('sort_order'),
            ])
            ->orderBy('sort_order')
            ->get();

        return response()->json(['data' => AcademicStructureResource::collection($levels)]);
    }
}
