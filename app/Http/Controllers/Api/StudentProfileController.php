<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Api\Concerns\ResolvesAuthenticatedStudent;
use App\Http\Controllers\Controller;
use App\Models\Student;
use App\Support\Countries;
use App\Support\MediaUrl;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class StudentProfileController extends Controller
{
    use ResolvesAuthenticatedStudent;

    public function show(Request $request): JsonResponse
    {
        $student = $this->student($request);
        $student->load(['academicLevel', 'academicYear', 'currentSemester']);
        $user = $request->user();

        return response()->json([
            'user' => ['name' => $user->name, 'email' => $user->email],
            'student' => $this->studentProfilePayload($student),
        ]);
    }

    public function update(Request $request): JsonResponse
    {
        $student = $this->student($request);
        $user = $request->user();

        $validated = $request->validate([
            'name' => ['sometimes', 'string', 'max:255'],
            'phone' => ['nullable', 'string', 'max:50'],
            'whatsapp' => ['nullable', 'string', 'max:50'],
            'country' => ['nullable', 'string', 'max:100'],
            'city' => ['nullable', 'string', 'max:100'],
        ]);

        if (isset($validated['name'])) {
            $user->update(['name' => $validated['name']]);
            $student->update(['name' => $validated['name']]);
        }

        $student->update(collect($validated)->except('name')->toArray());

        return response()->json([
            'message' => 'تم تحديث الملف الشخصي',
            'student' => $this->studentProfilePayload($student->fresh(['academicLevel', 'academicYear', 'currentSemester'])),
        ]);
    }

    /** @return array<string, mixed> */
    private function studentProfilePayload(Student $student): array
    {
        return [
            'first_name' => $student->first_name,
            'last_name' => $student->last_name,
            'phone' => $student->phone,
            'whatsapp' => $student->whatsapp,
            'gender' => $student->gender,
            'gender_label' => $student->genderLabel(),
            'birth_date' => $student->birth_date?->format('Y-m-d'),
            'nationality' => $student->nationality,
            'country' => $student->country,
            'country_label' => Countries::displayName($student->country),
            'city' => $student->city,
            'national_id' => $student->national_id,
            'education_level' => $student->education_level,
            'education_level_label' => $student->educationLevelLabel(),
            'heard_about' => $student->heard_about,
            'heard_about_label' => $student->heardAboutLabel(),
            'works_full_time' => $student->works_full_time,
            'works_full_time_label' => $student->booleanLabel($student->works_full_time),
            'participates_other_programs' => $student->participates_other_programs,
            'participates_other_programs_label' => $student->booleanLabel($student->participates_other_programs),
            'daily_hours' => $student->daily_hours,
            'daily_hours_label' => $student->dailyHoursLabel(),
            'terms_accepted_at' => $student->terms_accepted_at?->format('Y-m-d H:i'),
            'status' => $student->status,
            'status_label' => $student->statusLabel(),
            'rejection_reason' => $student->status === Student::STATUS_REJECTED
                ? $student->rejection_reason
                : null,
            'academic_level' => $student->academicLevel ? [
                'id' => $student->academicLevel->id,
                'name_ar' => $student->academicLevel->name_ar,
            ] : null,
            'academic_year' => $student->academicYear ? [
                'id' => $student->academicYear->id,
                'name_ar' => $student->academicYear->name_ar,
                'year_number' => $student->academicYear->year_number,
            ] : null,
            'current_semester' => $student->currentSemester ? [
                'id' => $student->currentSemester->id,
                'name_ar' => $student->currentSemester->name_ar,
                'semester_number' => $student->currentSemester->semester_number,
            ] : null,
            'photo' => MediaUrl::resolve($student->photo),
        ];
    }
}
