<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\ProgramResource;
use App\Models\Program;
use App\Support\ProgramCurriculumSubjects;
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

    public function show(Program $program, ProgramCurriculumSubjects $curriculumSubjects): JsonResponse
    {
        abort_unless($program->is_active, 404);

        $curriculum = $curriculumSubjects->forProgram($program);
        $program->curriculum_years = $curriculum['years'];
        $program->curriculum_subjects = $curriculum['subjects'];
        $program->curriculum_specializations = $curriculum['specializations'];

        return response()->json(['data' => new ProgramResource($program)]);
    }
}
