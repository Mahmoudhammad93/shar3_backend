<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Api\Concerns\ResolvesAuthenticatedStudent;
use App\Http\Controllers\Controller;
use App\Models\Certificate;
use App\Models\SiteSetting;
use App\Support\MediaUrl;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class StudentCertificateController extends Controller
{
    use ResolvesAuthenticatedStudent;

    public function show(Request $request, int $certificateId): JsonResponse
    {
        $student = $this->student($request);

        $certificate = Certificate::query()
            ->with(['course.teacher:id,name_ar,title_ar', 'student:id,name'])
            ->where('student_id', $student->id)
            ->findOrFail($certificateId);

        $settings = SiteSetting::current();

        return response()->json([
            'certificate' => [
                'id' => $certificate->id,
                'certificate_number' => $certificate->certificate_number,
                'issued_at' => $certificate->issued_at->format('Y-m-d'),
                'issued_at_label' => $certificate->issued_at->locale('ar')->translatedFormat('j F Y'),
                'student_name' => $certificate->student?->name ?? $student->name,
                'course_title' => $certificate->course?->title_ar,
                'teacher_name' => $certificate->course?->teacher?->name_ar,
                'teacher_title' => $certificate->course?->teacher?->title_ar,
                'institute_name' => $settings->dashboard_institute_name_ar ?: $settings->site_name_ar,
                'institute_tagline' => $settings->tagline_ar,
                'academic_year' => $settings->academic_year_ar,
                'file_url' => MediaUrl::resolve($certificate->file_path),
            ],
        ]);
    }
}
