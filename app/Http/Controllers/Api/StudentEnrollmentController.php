<?php

namespace App\Http\Controllers\Api;

use App\Actions\EnrollStudentAction;
use App\Http\Controllers\Api\Concerns\ResolvesAuthenticatedStudent;
use App\Http\Controllers\Controller;
use App\Models\Course;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class StudentEnrollmentController extends Controller
{
    use ResolvesAuthenticatedStudent;

    public function __construct(private readonly EnrollStudentAction $enrollStudent) {}

    public function store(Request $request): JsonResponse
    {
        $student = $this->student($request);

        $validated = $request->validate([
            'course_id' => ['required', 'exists:courses,id'],
            'notes' => ['nullable', 'string', 'max:1000'],
        ]);

        $course = Course::query()
            ->where('is_published', true)
            ->findOrFail($validated['course_id']);

        $result = $this->enrollStudent->execute(
            $student,
            $course,
            $validated['notes'] ?? null,
        );

        return response()->json([
            'message' => $result['message'],
            'enrollment' => ['status' => $result['enrollment']->status],
        ], $result['status_code']);
    }
}
