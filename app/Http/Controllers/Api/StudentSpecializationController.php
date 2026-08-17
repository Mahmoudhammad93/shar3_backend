<?php

namespace App\Http\Controllers\Api;

use App\Actions\ResolveStudentCurriculumAction;
use App\Actions\SyncStudentSpecializationsAction;
use App\Http\Controllers\Api\Concerns\ResolvesAuthenticatedStudent;
use App\Http\Controllers\Controller;
use App\Models\Specialization;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class StudentSpecializationController extends Controller
{
    use ResolvesAuthenticatedStudent;

    public function __construct(
        private readonly SyncStudentSpecializationsAction $syncSpecializations,
        private readonly ResolveStudentCurriculumAction $curriculum,
    ) {}

    public function index(Request $request): JsonResponse
    {
        $student = $this->student($request);
        $student->load(['academicLevel', 'academicYear', 'specializations']);

        return response()->json([
            'academic_level' => $student->academicLevel ? [
                'id' => $student->academicLevel->id,
                'name_ar' => $student->academicLevel->name_ar,
                'curriculum_type' => $student->academicLevel->curriculum_type?->value,
            ] : null,
            'academic_year' => $student->academicYear ? [
                'id' => $student->academicYear->id,
                'name_ar' => $student->academicYear->name_ar,
                'year_number' => $student->academicYear->year_number,
            ] : null,
            'specializations' => $student->specializations->map(fn (Specialization $spec) => [
                'id' => $spec->id,
                'name_ar' => $spec->name_ar,
                'name_en' => $spec->name_en,
                'slug' => $spec->slug,
                'status' => $spec->pivot->status,
                'selected_at' => $spec->pivot->selected_at?->toIso8601String(),
            ]),
        ]);
    }

    public function store(Request $request): JsonResponse
    {
        $student = $this->student($request);

        $validated = $request->validate([
            'specialization_ids' => ['required', 'array', 'min:1'],
            'specialization_ids.*' => ['integer', 'distinct'],
        ], [
            'specialization_ids.required' => 'يجب اختيار تخصص واحد على الأقل.',
            'specialization_ids.*.distinct' => 'لا يمكن تكرار نفس التخصص.',
        ]);

        $specializations = $this->syncSpecializations->execute(
            $student,
            $validated['specialization_ids'],
        );

        return response()->json([
            'message' => 'تم حفظ التخصصات بنجاح',
            'specializations' => $specializations,
        ]);
    }

    public function curriculum(Request $request): JsonResponse
    {
        $student = $this->student($request);

        return response()->json([
            'subjects' => $this->curriculum->forStudent($student)->values(),
        ]);
    }
}
