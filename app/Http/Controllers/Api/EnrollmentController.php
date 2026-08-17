<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Course;
use App\Models\Enrollment;
use App\Models\Student;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class EnrollmentController extends Controller
{
    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255'],
            'phone' => ['nullable', 'string', 'max:50'],
            'course_id' => ['required', 'exists:courses,id'],
            'notes' => ['nullable', 'string', 'max:1000'],
        ]);

        $course = Course::query()->where('is_published', true)->findOrFail($validated['course_id']);

        DB::transaction(function () use ($validated, $course) {
            $student = Student::query()->firstOrCreate(
                ['email' => $validated['email']],
                [
                    'name' => $validated['name'],
                    'phone' => $validated['phone'] ?? null,
                    'status' => Student::STATUS_PENDING,
                ]
            );

            $student->update([
                'name' => $validated['name'],
                'phone' => $validated['phone'] ?? $student->phone,
            ]);

            Enrollment::query()->firstOrCreate(
                [
                    'student_id' => $student->id,
                    'course_id' => $course->id,
                ],
                [
                    'status' => 'pending',
                    'notes' => $validated['notes'] ?? null,
                ]
            );
        });

        return response()->json([
            'message' => 'تم استلام طلب التسجيل بنجاح. سيتم مراجعته من قبل الإدارة.',
            'enrollment' => ['status' => 'pending'],
        ], 201);
    }
}
