<?php

namespace Tests\Feature;

use App\Enums\CurriculumType;
use App\Models\AcademicLevel;
use App\Models\AcademicYear;
use App\Models\CurriculumAssignment;
use App\Models\Semester;
use App\Models\Specialization;
use App\Models\Subject;
use Database\Seeders\AcademicStructureSeeder;
use Illuminate\Database\QueryException;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AcademicStructureTest extends TestCase
{
    use RefreshDatabase;

    public function test_academic_structure_returns_three_levels(): void
    {
        $this->seed(AcademicStructureSeeder::class);

        $response = $this->getJson('/api/v1/academic/structure');

        $response->assertOk()
            ->assertJsonCount(3, 'data')
            ->assertJsonPath('data.0.curriculum_type', CurriculumType::General->value)
            ->assertJsonPath('data.2.curriculum_type', CurriculumType::Specialized->value);
    }

    public function test_general_level_includes_years_with_subjects(): void
    {
        $this->seed(AcademicStructureSeeder::class);

        $response = $this->getJson('/api/v1/academic/structure');

        $preparatory = collect($response->json('data'))->firstWhere('slug', 'preparatory-level');

        $this->assertNotNull($preparatory);
        $this->assertNotEmpty($preparatory['years']);
        $this->assertNotEmpty($preparatory['years'][0]['semesters'][0]['subjects']);
    }

    public function test_specialized_level_includes_specializations_with_curriculum(): void
    {
        $this->seed(AcademicStructureSeeder::class);

        $response = $this->getJson('/api/v1/academic/structure');

        $specialized = collect($response->json('data'))->firstWhere('slug', 'specialized-level');

        $this->assertNotNull($specialized);
        $this->assertArrayHasKey('specializations', $specialized);
        $this->assertNotEmpty($specialized['specializations']);
        $this->assertNotEmpty($specialized['specializations'][0]['years']);
    }

    public function test_specialized_level_has_specializations_general_level_does_not(): void
    {
        $this->seed(AcademicStructureSeeder::class);

        $general = AcademicLevel::query()->where('slug', 'preparatory-level')->firstOrFail();
        $specialized = AcademicLevel::query()->where('slug', 'specialized-level')->firstOrFail();

        $this->assertTrue($specialized->isSpecialized());
        $this->assertTrue($general->isGeneral());
        $this->assertEmpty($general->specializations);
        $this->assertNotEmpty($specialized->specializations);
    }

    public function test_curriculum_assignment_prevents_duplicate_subject_in_same_semester(): void
    {
        $level = AcademicLevel::query()->create([
            'name_ar' => 'عام',
            'slug' => 'level-dup',
            'number' => 20,
            'curriculum_type' => CurriculumType::General,
            'is_active' => true,
        ]);

        $year = AcademicYear::query()->create([
            'academic_level_id' => $level->id,
            'name_ar' => 'سنة',
            'slug' => 'year-dup',
            'year_number' => 1,
            'is_active' => true,
        ]);

        $semester = Semester::query()->create([
            'academic_year_id' => $year->id,
            'name_ar' => 'فصل',
            'slug' => 'sem-dup',
            'semester_number' => 1,
            'is_active' => true,
        ]);

        $subject = Subject::query()->create([
            'name_ar' => 'مادة',
            'slug' => 'subject-dup',
            'is_active' => true,
        ]);

        CurriculumAssignment::query()->create([
            'semester_id' => $semester->id,
            'subject_id' => $subject->id,
            'specialization_id' => null,
            'is_active' => true,
        ]);

        $spec = Specialization::query()->create([
            'academic_level_id' => AcademicLevel::query()->create([
                'name_ar' => 'متخصص',
                'slug' => 'spec-level-dup',
                'number' => 21,
                'curriculum_type' => CurriculumType::Specialized,
                'is_active' => true,
            ])->id,
            'name_ar' => 'تخصص',
            'slug' => 'spec-for-dup',
            'is_active' => true,
        ]);

        CurriculumAssignment::query()->create([
            'semester_id' => $semester->id,
            'subject_id' => $subject->id,
            'specialization_id' => $spec->id,
            'is_active' => true,
        ]);

        $this->expectException(QueryException::class);

        CurriculumAssignment::query()->create([
            'semester_id' => $semester->id,
            'subject_id' => $subject->id,
            'specialization_id' => $spec->id,
            'is_active' => true,
        ]);
    }
}
