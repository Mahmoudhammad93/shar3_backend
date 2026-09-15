<?php

namespace Tests\Feature;

use App\Models\AcademicLevel;
use App\Models\AcademicYear;
use App\Models\Specialization;
use Database\Seeders\AcademicStructureSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class StudentCurriculumTest extends TestCase
{
    use RefreshDatabase;

    public function test_student_with_one_specialization_gets_its_subjects(): void
    {
        $this->seed(AcademicStructureSeeder::class);

        $level = AcademicLevel::query()->where('slug', 'specialized-level')->firstOrFail();
        $year = AcademicYear::query()->where('slug', 'fourth-year')->firstOrFail();
        $spec = Specialization::query()->where('slug', 'fiqh-usul')->firstOrFail();

        $student = $this->createStudent([
            'academic_level_id' => $level->id,
            'academic_year_id' => $year->id,
        ]);

        $student->specializations()->attach($spec->id, [
            'status' => 'active',
            'selected_at' => now(),
        ]);

        $this->actingAsStudent($student);

        $response = $this->getJson('/api/v1/student/curriculum');

        $response->assertOk();

        $slugs = collect($response->json('subjects'))->pluck('slug');

        $this->assertTrue($slugs->contains('fq-y4s1-usul'));
        $this->assertTrue($slugs->contains('research-methods'));
    }

    public function test_student_with_multiple_specializations_gets_merged_subjects_without_duplicates(): void
    {
        $this->seed(AcademicStructureSeeder::class);

        $level = AcademicLevel::query()->where('slug', 'specialized-level')->firstOrFail();
        $year = AcademicYear::query()->where('slug', 'fourth-year')->firstOrFail();

        $student = $this->createStudent([
            'academic_level_id' => $level->id,
            'academic_year_id' => $year->id,
        ]);

        $specs = Specialization::query()
            ->whereIn('slug', ['fiqh-usul', 'hadith-sciences'])
            ->get();

        foreach ($specs as $spec) {
            $student->specializations()->attach($spec->id, [
                'status' => 'active',
                'selected_at' => now(),
            ]);
        }

        $this->actingAsStudent($student);

        $response = $this->getJson('/api/v1/student/curriculum');

        $subjects = collect($response->json('subjects'));

        $researchEntries = $subjects->where('slug', 'research-methods');

        $this->assertCount(1, $researchEntries);
        $this->assertCount(2, $researchEntries->first()['specializations']);
        $this->assertGreaterThanOrEqual(3, $subjects->count());
    }

    public function test_preparatory_student_gets_general_curriculum_only(): void
    {
        $this->seed(AcademicStructureSeeder::class);

        $level = AcademicLevel::query()->where('slug', 'preparatory-level')->firstOrFail();
        $year = AcademicYear::query()->where('slug', 'first-year')->firstOrFail();

        $this->actingAsStudent($this->createStudent([
            'academic_level_id' => $level->id,
            'academic_year_id' => $year->id,
        ]));

        $response = $this->getJson('/api/v1/student/curriculum');

        $response->assertOk();

        $slugs = collect($response->json('subjects'))->pluck('slug');

        $this->assertTrue($slugs->contains('y1s1-fiqh'));
        $this->assertFalse($slugs->contains('fq-y4s1-usul'));
    }
}
