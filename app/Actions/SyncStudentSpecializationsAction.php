<?php

namespace App\Actions;

use App\Enums\CurriculumType;
use App\Enums\StudentSpecializationStatus;
use App\Models\Specialization;
use App\Models\Student;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class SyncStudentSpecializationsAction
{
    /**
     * @param  array<int, int>  $specializationIds
     * @return array<int, array<string, mixed>>
     */
    public function execute(Student $student, array $specializationIds): array
    {
        $student->loadMissing('academicLevel');

        if ($student->academicLevel?->curriculum_type !== CurriculumType::Specialized) {
            throw ValidationException::withMessages([
                'specialization_ids' => ['يمكن اختيار التخصصات فقط في المستوى المتخصص.'],
            ]);
        }

        $specializationIds = collect($specializationIds)
            ->filter(fn ($id) => is_numeric($id))
            ->map(fn ($id) => (int) $id)
            ->unique()
            ->values()
            ->all();

        if ($specializationIds === []) {
            throw ValidationException::withMessages([
                'specialization_ids' => ['يجب اختيار تخصص واحد على الأقل.'],
            ]);
        }

        $specializations = Specialization::query()
            ->whereIn('id', $specializationIds)
            ->where('is_active', true)
            ->where('academic_level_id', $student->academic_level_id)
            ->get();

        if ($specializations->count() !== count($specializationIds)) {
            throw ValidationException::withMessages([
                'specialization_ids' => ['واحد أو أكثر من التخصصات غير صالح أو غير نشط أو لا ينتمي لمستواك الأكاديمي.'],
            ]);
        }

        return DB::transaction(function () use ($student, $specializationIds) {
            $syncData = [];

            foreach ($specializationIds as $id) {
                $syncData[$id] = [
                    'status' => StudentSpecializationStatus::Active->value,
                    'selected_at' => now(),
                ];
            }

            $student->specializations()->sync($syncData);

            return $student->specializations()
                ->get()
                ->map(fn (Specialization $specialization) => [
                    'id' => $specialization->id,
                    'name_ar' => $specialization->name_ar,
                    'name_en' => $specialization->name_en,
                    'slug' => $specialization->slug,
                    'status' => $specialization->pivot->status,
                    'selected_at' => optional($specialization->pivot->selected_at)->toIso8601String()
                        ?? $specialization->pivot->selected_at,
                ])
                ->all();
        });
    }
}
