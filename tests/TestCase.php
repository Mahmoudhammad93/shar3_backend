<?php

namespace Tests;

use App\Enums\CurriculumType;
use App\Models\AcademicLevel;
use App\Models\AcademicYear;
use App\Models\Semester;
use App\Models\Student;
use App\Models\User;
use Illuminate\Foundation\Testing\TestCase as BaseTestCase;
use Laravel\Sanctum\Sanctum;

abstract class TestCase extends BaseTestCase
{
    protected function actingAsStudent(?Student $student = null): Student
    {
        $student ??= $this->createStudent();
        Sanctum::actingAs($student->user);

        return $student;
    }

    protected function createStudent(array $attributes = []): Student
    {
        $userAttributes = array_merge(
            ['role' => 'student'],
            $attributes['user'] ?? [],
            array_intersect_key($attributes, array_flip(['email', 'name', 'password']))
        );
        unset($attributes['user'], $attributes['name'], $attributes['password']);

        $user = User::factory()->create($userAttributes);

        $student = Student::query()->create(array_merge([
            'user_id' => $user->id,
            'name' => $user->name,
            'email' => $user->email,
            'status' => Student::STATUS_ACTIVE,
            'terms_accepted_at' => now(),
        ], $attributes));

        if ($student->status === Student::STATUS_ACTIVE) {
            $student = $this->ensureActiveStudentAcademicPlacement($student);
        }

        return $student;
    }

    protected function createPendingStudent(array $attributes = []): Student
    {
        return $this->createStudent(array_merge([
            'status' => Student::STATUS_PENDING,
            'academic_level_id' => null,
            'academic_year_id' => null,
            'current_semester_id' => null,
        ], $attributes));
    }

    protected function ensureActiveStudentAcademicPlacement(Student $student): Student
    {
        if ($student->academic_level_id && $student->academic_year_id && $student->current_semester_id) {
            return $student;
        }

        if ($student->academic_year_id) {
            $year = AcademicYear::query()->findOrFail($student->academic_year_id);
            $semester = Semester::query()
                ->where('academic_year_id', $year->id)
                ->where('is_active', true)
                ->orderBy('semester_number')
                ->first()
                ?? Semester::query()->create([
                    'academic_year_id' => $year->id,
                    'name_ar' => 'الفصل الأول',
                    'name_en' => 'First Semester',
                    'slug' => $year->slug.'-semester-1',
                    'semester_number' => 1,
                    'sort_order' => 1,
                    'is_active' => true,
                ]);

            $student->update([
                'academic_level_id' => $student->academic_level_id ?? $year->academic_level_id,
                'current_semester_id' => $semester->id,
            ]);

            return $student->fresh();
        }

        $level = AcademicLevel::query()->where('slug', 'preparatory-level')->first()
            ?? AcademicLevel::query()->create([
                'name_ar' => 'المستوى التمهيدي',
                'name_en' => 'Preparatory',
                'slug' => 'preparatory-level',
                'number' => 1,
                'curriculum_type' => CurriculumType::General,
                'sort_order' => 1,
                'is_active' => true,
            ]);

        $year = AcademicYear::query()->where('slug', 'first-year')->first()
            ?? AcademicYear::query()->create([
                'academic_level_id' => $level->id,
                'name_ar' => 'السنة الأولى',
                'name_en' => 'First Year',
                'slug' => 'first-year',
                'year_number' => 1,
                'sort_order' => 1,
                'is_active' => true,
            ]);

        $semester = Semester::query()
            ->where('academic_year_id', $year->id)
            ->where('is_active', true)
            ->orderBy('semester_number')
            ->first()
            ?? Semester::query()->create([
                'academic_year_id' => $year->id,
                'name_ar' => 'الفصل الأول',
                'name_en' => 'First Semester',
                'slug' => 'first-year-semester-1',
                'semester_number' => 1,
                'sort_order' => 1,
                'is_active' => true,
            ]);

        $student->update([
            'academic_level_id' => $level->id,
            'academic_year_id' => $year->id,
            'current_semester_id' => $semester->id,
        ]);

        return $student->fresh();
    }
}
