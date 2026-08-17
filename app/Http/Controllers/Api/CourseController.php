<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\CourseResource;
use App\Models\Course;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class CourseController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $courses = Course::query()
            ->with(['category', 'program', 'teacher'])
            ->where('is_published', true)
            ->when($request->category, fn ($q, $slug) => $q->whereHas('category', fn ($c) => $c->where('slug', $slug)))
            ->when($request->program, fn ($q, $slug) => $q->whereHas('program', fn ($p) => $p->where('slug', $slug)))
            ->when($request->search, fn ($q, $search) => $q->where(function ($query) use ($search) {
                $query->where('title_ar', 'like', "%{$search}%")
                    ->orWhere('title_en', 'like', "%{$search}%");
            }))
            ->orderBy('sort_order')
            ->paginate(12);

        return response()->json([
            'data' => CourseResource::collection($courses),
            'meta' => [
                'current_page' => $courses->currentPage(),
                'last_page' => $courses->lastPage(),
                'total' => $courses->total(),
            ],
        ]);
    }

    public function show(Course $course): JsonResponse
    {
        abort_unless($course->is_published, 404);

        $course->load(['category', 'program', 'teacher', 'lessons' => fn ($q) => $q->where('is_published', true)]);

        return response()->json([
            'data' => new CourseResource($course),
        ]);
    }
}
