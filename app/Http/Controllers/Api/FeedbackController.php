<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\ErrorReport;
use App\Models\VolunteerApplication;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class FeedbackController extends Controller
{
    public function reportError(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'lesson_id' => ['nullable', 'exists:lessons,id'],
            'page_url' => ['nullable', 'string', 'max:500'],
            'error_type' => ['required', 'in:broken_link,video,content,other'],
            'description' => ['required', 'string', 'max:2000'],
        ], [
            'description.required' => 'يرجى وصف المشكلة.',
            'error_type.required' => 'يرجى اختيار نوع الخطأ.',
        ]);

        $studentId = $request->user()?->student?->id;

        ErrorReport::query()->create([
            ...$validated,
            'student_id' => $studentId,
        ]);

        return response()->json(['message' => 'تم إرسال البلاغ. جزاك الله خيراً.']);
    }

    public function volunteer(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'country' => ['required', 'string', 'max:100'],
            'phone' => ['required', 'string', 'max:50'],
            'whatsapp' => ['nullable', 'string', 'max:50'],
            'work_type' => ['required', 'string', 'max:255'],
            'experience' => ['nullable', 'string', 'max:2000'],
        ]);

        VolunteerApplication::query()->create($validated);

        return response()->json(['message' => 'تم استلام طلب التطوع. سنتواصل معك قريباً إن شاء الله.']);
    }
}
