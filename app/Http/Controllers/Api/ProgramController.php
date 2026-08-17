<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\ProgramResource;
use App\Models\Program;
use Illuminate\Http\JsonResponse;

class ProgramController extends Controller
{
    public function index(): JsonResponse
    {
        $programs = Program::query()
            ->where('is_active', true)
            ->withCount(['courses' => fn ($q) => $q->where('is_published', true)])
            ->orderBy('sort_order')
            ->get();

        return response()->json(['data' => ProgramResource::collection($programs)]);
    }

    public function show(Program $program): JsonResponse
    {
        abort_unless($program->is_active, 404);

        $program->load(['courses' => fn ($q) => $q->where('is_published', true)->with('teacher')]);

        return response()->json(['data' => new ProgramResource($program)]);
    }
}
