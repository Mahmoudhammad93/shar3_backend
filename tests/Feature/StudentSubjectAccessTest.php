<?php

namespace Tests\Feature;

use App\Models\AcademicLevel;
use App\Models\AcademicYear;
use App\Models\Subject;
use Database\Seeders\AcademicStructureSeeder;
use Database\Seeders\SubjectCourseSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class StudentSubjectAccessTest extends TestCase
{
    use RefreshDatabase;

    public function test_active_student_can_open_curriculum_subject_lessons_without_course_enrollment(): void
    {
        $this->seed(AcademicStructureSeeder::class);
        $this->seed(SubjectCourseSeeder::class);

        $level = AcademicLevel::query()->where('slug', 'preparatory-level')->firstOrFail();
        $year = AcademicYear::query()->where('slug', 'first-year')->firstOrFail();
        $subject = Subject::query()->where('slug', 'y1s1-fiqh')->firstOrFail();

        $student = $this->actingAsStudent($this->createStudent([
            'academic_level_id' => $level->id,
            'academic_year_id' => $year->id,
        ]));

        $this->getJson('/api/v1/student/subjects/'.$subject->slug)
            ->assertOk()
            ->assertJsonPath('subject.slug', 'y1s1-fiqh')
            ->assertJsonStructure(['course', 'lessons', 'progress']);
    }

    public function test_student_cannot_open_subject_outside_curriculum(): void
    {
        $this->seed(AcademicStructureSeeder::class);
        $this->seed(SubjectCourseSeeder::class);

        $level = AcademicLevel::query()->where('slug', 'preparatory-level')->firstOrFail();
        $year = AcademicYear::query()->where('slug', 'first-year')->firstOrFail();
        $subject = Subject::query()->where('slug', 'fq-y4s1-usul')->first();

        if (! $subject) {
            $this->markTestSkipped('Specialized subject not seeded.');
        }

        $this->actingAsStudent($this->createStudent([
            'academic_level_id' => $level->id,
            'academic_year_id' => $year->id,
        ]));

        $this->getJson('/api/v1/student/subjects/'.$subject->slug)->assertNotFound();
    }
}
