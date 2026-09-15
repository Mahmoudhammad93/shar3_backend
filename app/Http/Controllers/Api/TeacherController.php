<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\TeacherResource;
use App\Models\Teacher;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class TeacherController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $teachers = Teacher::query()
            ->where('is_active', true)
            ->when($request->boolean('featured'), fn ($query) => $query->where('is_featured', true)->limit(4))
            ->withCount('courses')
            ->orderBy('sort_order')
            ->get();

        return response()->json(['data' => TeacherResource::collection($teachers)]);
    }

    public function show(Teacher $teacher): JsonResponse
    {
        abort_unless($teacher->is_active, 404);

        $teacher->load(['courses' => fn ($q) => $q->where('is_published', true)]);

        return response()->json(['data' => new TeacherResource($teacher)]);
    }
}
