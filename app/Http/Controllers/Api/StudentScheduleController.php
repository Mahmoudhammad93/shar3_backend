<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Api\Concerns\ResolvesAuthenticatedStudent;
use App\Http\Controllers\Controller;
use App\Models\Enrollment;
use App\Models\Schedule;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class StudentScheduleController extends Controller
{
    use ResolvesAuthenticatedStudent;

    public function __invoke(Request $request): JsonResponse
    {
        $student = $this->student($request);

        $courseIds = Enrollment::query()
            ->where('student_id', $student->id)
            ->where('status', Enrollment::STATUS_APPROVED)
            ->pluck('course_id');

        $schedules = Schedule::query()
            ->with('course:id,title_ar')
            ->whereIn('course_id', $courseIds)
            ->where('is_published', true)
            ->where('starts_at', '>=', now()->subDays(7))
            ->orderBy('starts_at')
            ->get();

        return response()->json([
            'data' => $schedules->map(fn (Schedule $schedule) => [
                'id' => $schedule->id,
                'title_ar' => $schedule->title_ar,
                'description_ar' => $schedule->description_ar,
                'type' => $schedule->type,
                'starts_at' => $schedule->starts_at->format('Y-m-d H:i'),
                'ends_at' => $schedule->ends_at?->format('Y-m-d H:i'),
                'meeting_url' => $schedule->meeting_url,
                'course' => $schedule->course?->title_ar,
            ]),
        ]);
    }
}
