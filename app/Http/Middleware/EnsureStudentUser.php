<?php

namespace App\Http\Middleware;

use App\Models\Student;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureStudentUser
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if (! $user || $user->role !== 'student') {
            return response()->json(['message' => 'غير مصرح'], 403);
        }

        if (! $user->student) {
            return response()->json(['message' => 'Student profile not found'], 403);
        }

        if ($user->student->status === Student::STATUS_SUSPENDED) {
            return response()->json(['message' => 'تم إيقاف حسابك. يرجى التواصل مع الإدارة.'], 403);
        }

        return $next($request);
    }
}
