<?php

namespace App\Http\Middleware;

use App\Models\Student;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureActiveStudent
{
    public function handle(Request $request, Closure $next): Response
    {
        $student = $request->user()?->student;

        if (! $student) {
            return response()->json(['message' => 'Student profile not found'], 403);
        }

        if ($student->status === Student::STATUS_PENDING) {
            return response()->json([
                'message' => 'طلب التسجيل قيد المراجعة من الإدارة',
                'status' => 'pending',
            ], 403);
        }

        if ($student->status === Student::STATUS_REJECTED) {
            return response()->json([
                'message' => 'تم رفض طلب التسجيل',
                'status' => 'rejected',
                'rejection_reason' => $student->rejection_reason,
            ], 403);
        }

        if ($student->status === Student::STATUS_SUSPENDED) {
            return response()->json(['message' => 'تم إيقاف حسابك. يرجى التواصل مع الإدارة.'], 403);
        }

        if ($student->status !== Student::STATUS_ACTIVE) {
            return response()->json(['message' => 'غير مصرح'], 403);
        }

        if (! $student->academic_level_id || ! $student->academic_year_id || ! $student->current_semester_id) {
            return response()->json([
                'message' => 'لم يكتمل تفعيل حسابك الأكاديمي بعد. يرجى التواصل مع الإدارة.',
                'status' => 'incomplete_onboarding',
            ], 403);
        }

        return $next($request);
    }
}
