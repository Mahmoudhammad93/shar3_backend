<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Api\Concerns\ResolvesAuthenticatedStudent;
use App\Http\Controllers\Controller;
use App\Models\Certificate;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class StudentGradeController extends Controller
{
    use ResolvesAuthenticatedStudent;

    public function __invoke(Request $request): JsonResponse
    {
        $student = $this->student($request);

        $submissions = $student->assignmentSubmissions()
            ->with(['assignment.course:id,title_ar'])
            ->where('status', 'graded')
            ->get();

        $certificates = Certificate::query()
            ->with('course:id,title_ar')
            ->where('student_id', $student->id)
            ->get();

        return response()->json([
            'grades' => $submissions->map(fn ($submission) => [
                'assignment' => $submission->assignment?->title_ar,
                'course' => $submission->assignment?->course?->title_ar,
                'score' => $submission->score,
                'max_score' => $submission->assignment?->max_score,
                'feedback' => $submission->feedback,
            ]),
            'certificates' => $certificates->map(fn (Certificate $certificate) => [
                'id' => $certificate->id,
                'course' => $certificate->course?->title_ar,
                'certificate_number' => $certificate->certificate_number,
                'issued_at' => $certificate->issued_at->format('Y-m-d'),
            ]),
        ]);
    }
}
