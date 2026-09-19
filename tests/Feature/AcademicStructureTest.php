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

    public function test_specialized_level_keeps_year_four_and_five_semester_buckets_separate(): void
    {
        $this->seed(AcademicStructureSeeder::class);

        $response = $this->getJson('/api/v1/academic/structure');

        $specialized = collect($response->json('data'))->firstWhere('slug', 'specialized-level');
        $this->assertNotNull($specialized);
        $this->assertNotEmpty($specialized['specializations']);

        $firstSpec = $specialized['specializations'][0];
        $years = collect($firstSpec['years']);

        $this->assertGreaterThanOrEqual(
            2,
            $years->count(),
            'Specialization curriculum must keep Year 4 and Year 5 as separate year buckets'
        );

        $yearNumbers = $years->pluck('year_number')->all();
        $this->assertSame(
            $yearNumbers,
            array_values(array_unique($yearNumbers)),
            'Must not collapse multiple academic years into one year bucket'
        );

        $year4 = $years->firstWhere('year_number', 4);
        $year5 = $years->firstWhere('year_number', 5);

        $this->assertNotNull($year4);
        $this->assertNotNull($year5);
        $this->assertNotSame($year4['id'], $year5['id']);

        $year4SemesterNumbers = collect($year4['semesters'])->pluck('semester_number')->all();
        $year5SemesterNumbers = collect($year5['semesters'])->pluck('semester_number')->all();

        $this->assertSame(
            $year4SemesterNumbers,
            array_values(array_unique($year4SemesterNumbers)),
            'Year 4 must not list duplicate semester numbers'
        );
        $this->assertSame(
            $year5SemesterNumbers,
            array_values(array_unique($year5SemesterNumbers)),
            'Year 5 must not list duplicate semester numbers'
        );

        $year4SemesterIds = collect($year4['semesters'])->pluck('id')->all();
        $year5SemesterIds = collect($year5['semesters'])->pluck('id')->all();
        $this->assertEmpty(
            array_intersect($year4SemesterIds, $year5SemesterIds),
            'Semester 1/2 in Year 4 must not merge with Semester 1/2 in Year 5'
        );
    }

    public function test_specialization_years_payload_groups_by_academic_year_id_not_missing_year_id(): void
    {
        $level = AcademicLevel::query()->create([
            'name_ar' => 'متخصص اختبار',
            'slug' => 'spec-level-group-test',
            'number' => 90,
            'curriculum_type' => CurriculumType::Specialized,
            'is_active' => true,
            'sort_order' => 90,
        ]);

        $year4 = AcademicYear::query()->create([
            'academic_level_id' => $level->id,
            'name_ar' => 'السنة الرابعة',
            'slug' => 'group-test-year-4',
            'year_number' => 4,
            'is_active' => true,
            'sort_order' => 4,
        ]);

        $year5 = AcademicYear::query()->create([
            'academic_level_id' => $level->id,
            'name_ar' => 'السنة الخامسة',
            'slug' => 'group-test-year-5',
            'year_number' => 5,
            'is_active' => true,
            'sort_order' => 5,
        ]);

        $y4s1 = Semester::query()->create([
            'academic_year_id' => $year4->id,
            'name_ar' => 'الفصل الأول',
            'slug' => 'group-test-y4-s1',
            'semester_number' => 1,
            'is_active' => true,
            'sort_order' => 1,
        ]);
        $y4s2 = Semester::query()->create([
            'academic_year_id' => $year4->id,
            'name_ar' => 'الفصل الثاني',
            'slug' => 'group-test-y4-s2',
            'semester_number' => 2,
            'is_active' => true,
            'sort_order' => 2,
        ]);
        $y5s1 = Semester::query()->create([
            'academic_year_id' => $year5->id,
            'name_ar' => 'الفصل الأول',
            'slug' => 'group-test-y5-s1',
            'semester_number' => 1,
            'is_active' => true,
            'sort_order' => 1,
        ]);
        $y5s2 = Semester::query()->create([
            'academic_year_id' => $year5->id,
            'name_ar' => 'الفصل الثاني',
            'slug' => 'group-test-y5-s2',
            'semester_number' => 2,
            'is_active' => true,
            'sort_order' => 2,
        ]);

        $spec = Specialization::query()->create([
            'academic_level_id' => $level->id,
            'name_ar' => 'تخصص تجريبي',
            'slug' => 'group-test-spec',
            'is_active' => true,
            'sort_order' => 1,
        ]);

        foreach ([$y4s1, $y4s2, $y5s1, $y5s2] as $index => $semester) {
            $subject = Subject::query()->create([
                'name_ar' => 'مقرر '.$index,
                'slug' => 'group-test-subject-'.$index,
                'is_active' => true,
            ]);

            CurriculumAssignment::query()->create([
                'semester_id' => $semester->id,
                'subject_id' => $subject->id,
                'specialization_id' => $spec->id,
                'is_active' => true,
                'sort_order' => $index,
            ]);
        }

        $response = $this->getJson('/api/v1/academic/structure');
        $response->assertOk();

        $payload = collect($response->json('data'))->firstWhere('slug', 'spec-level-group-test');
        $this->assertNotNull($payload);

        $specPayload = collect($payload['specializations'])->firstWhere('slug', 'group-test-spec');
        $this->assertNotNull($specPayload);

        $years = collect($specPayload['years']);
        $this->assertCount(2, $years);
        $this->assertSame([4, 5], $years->pluck('year_number')->values()->all());

        $year4 = $years->firstWhere('year_number', 4);
        $year5 = $years->firstWhere('year_number', 5);

        $this->assertCount(2, $year4['semesters']);
        $this->assertCount(2, $year5['semesters']);
        $this->assertSame([1, 2], collect($year4['semesters'])->pluck('semester_number')->values()->all());
        $this->assertSame([1, 2], collect($year5['semesters'])->pluck('semester_number')->values()->all());
        $this->assertSame($y4s1->id, $year4['semesters'][0]['id']);
        $this->assertSame($y5s1->id, $year5['semesters'][0]['id']);
        $this->assertNotSame($year4['semesters'][0]['id'], $year5['semesters'][0]['id']);
    }

    public function test_general_level_years_remain_nested_under_level_not_specializations(): void
    {
        $this->seed(AcademicStructureSeeder::class);

        $response = $this->getJson('/api/v1/academic/structure');

        $preparatory = collect($response->json('data'))->firstWhere('slug', 'preparatory-level');
        $this->assertNotNull($preparatory);
        $this->assertNotEmpty($preparatory['years']);
        $this->assertSame([], $preparatory['specializations'] ?? []);

        foreach ($preparatory['years'] as $year) {
            $semesterNumbers = collect($year['semesters'])->pluck('semester_number')->all();
            $this->assertSame(
                $semesterNumbers,
                array_values(array_unique($semesterNumbers)),
                'General curriculum must not duplicate semester numbers within a year'
            );
        }
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
