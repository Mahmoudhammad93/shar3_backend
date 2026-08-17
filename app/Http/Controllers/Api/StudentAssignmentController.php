<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Api\Concerns\ResolvesAuthenticatedStudent;
use App\Http\Controllers\Controller;
use App\Models\Assignment;
use App\Models\Enrollment;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class StudentAssignmentController extends Controller
{
    use ResolvesAuthenticatedStudent;

    public function index(Request $request): JsonResponse
    {
        $student = $this->student($request);

        $courseIds = Enrollment::query()
            ->where('student_id', $student->id)
            ->where('status', Enrollment::STATUS_APPROVED)
            ->pluck('course_id');

        $assignments = Assignment::query()
            ->with(['course:id,title_ar', 'submissions' => fn ($query) => $query->where('student_id', $student->id)])
            ->whereIn('course_id', $courseIds)
            ->where('is_published', true)
            ->orderBy('due_at')
            ->get();

        return response()->json([
            'data' => $assignments->map(fn (Assignment $assignment) => [
                'id' => $assignment->id,
                'title_ar' => $assignment->title_ar,
                'description_ar' => $assignment->description_ar,
                'due_at' => $assignment->due_at?->format('Y-m-d H:i'),
                'max_score' => $assignment->max_score,
                'course' => $assignment->course?->title_ar,
                'submission' => $assignment->submissions->first() ? [
                    'status' => $assignment->submissions->first()->status,
                    'score' => $assignment->submissions->first()->score,
                    'submitted_at' => $assignment->submissions->first()->submitted_at?->format('Y-m-d H:i'),
                ] : null,
            ]),
        ]);
    }

    public function submit(Request $request, int $assignmentId): JsonResponse
    {
        $student = $this->student($request);
        $assignment = Assignment::query()->findOrFail($assignmentId);

        $enrolled = Enrollment::query()
            ->where('student_id', $student->id)
            ->where('course_id', $assignment->course_id)
            ->where('status', Enrollment::STATUS_APPROVED)
            ->exists();

        abort_unless($enrolled, 403);

        $validated = $request->validate([
            'content' => ['required', 'string', 'max:10000'],
        ]);

        $student->assignmentSubmissions()->updateOrCreate(
            ['assignment_id' => $assignmentId],
            ['content' => $validated['content'], 'status' => 'submitted', 'submitted_at' => now()]
        );

        return response()->json(['message' => 'تم تسليم الواجب بنجاح']);
    }
}
