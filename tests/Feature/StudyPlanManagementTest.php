<?php

namespace Tests\Feature;

use App\Filament\Resources\Subjects\Schemas\StudyPlanAssignmentForm;
use App\Models\Course;
use App\Models\CurriculumAssignment;
use App\Models\Semester;
use Database\Seeders\AcademicStructureSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Validation\ValidationException;
use Tests\TestCase;

class StudyPlanManagementTest extends TestCase
{
    use RefreshDatabase;

    public function test_study_plan_edit_updates_subject_and_assignment_fields(): void
    {
        $this->seed(AcademicStructureSeeder::class);

        $assignment = CurriculumAssignment::query()->with('subject')->firstOrFail();

        StudyPlanAssignmentForm::saveEdit($assignment, [
            'semester_id' => $assignment->semester_id,
            'specialization_id' => $assignment->specialization_id,
            'name_ar' => 'مادة محدثة',
            'name_en' => null,
            'course_id' => null,
            'memorization_ar' => 'حفظ تجريبي',
            'primary_text_ar' => 'كتاب تجريبي',
            'supplementary_text_ar' => 'تكميلي تجريبي',
            'sort_order' => 99,
            'is_required' => false,
            'is_active' => false,
        ]);

        $assignment->refresh();
        $assignment->subject?->refresh();

        $this->assertSame('مادة محدثة', $assignment->subject?->name_ar);
        $this->assertSame('كتاب تجريبي', $assignment->subject?->primary_text_ar);
        $this->assertSame(99, $assignment->sort_order);
        $this->assertFalse($assignment->is_required);
        $this->assertFalse($assignment->is_active);
    }

    public function test_study_plan_create_adds_subject_to_curriculum(): void
    {
        $this->seed(AcademicStructureSeeder::class);

        $semesterId = CurriculumAssignment::query()->value('semester_id');

        $assignment = StudyPlanAssignmentForm::createAssignment([
            'semester_id' => $semesterId,
            'specialization_id' => null,
            'name_ar' => 'مادة جديدة',
            'slug' => 'new-study-plan-subject',
            'memorization_ar' => null,
            'primary_text_ar' => 'متن جديد',
            'supplementary_text_ar' => null,
            'sort_order' => 1,
            'is_required' => true,
            'is_active' => true,
        ]);

        $this->assertDatabaseHas('subjects', [
            'slug' => 'new-study-plan-subject',
            'primary_text_ar' => 'متن جديد',
        ]);

        $this->assertDatabaseHas('curriculum_assignments', [
            'id' => $assignment->id,
            'semester_id' => $semesterId,
            'subject_id' => $assignment->subject_id,
        ]);
    }

    public function test_study_plan_edit_rejects_course_already_linked_to_another_subject(): void
    {
        $this->seed(AcademicStructureSeeder::class);

        $course = Course::query()->create([
            'title_ar' => 'دورة مشتركة',
            'slug' => 'shared-course',
            'is_published' => true,
        ]);

        $assignments = CurriculumAssignment::query()->with('subject')->limit(2)->get();
        $assignments[0]->subject?->update(['course_id' => $course->id]);

        $this->expectException(ValidationException::class);

        StudyPlanAssignmentForm::saveEdit($assignments[1], [
            'semester_id' => $assignments[1]->semester_id,
            'specialization_id' => $assignments[1]->specialization_id,
            'name_ar' => $assignments[1]->subject?->name_ar,
            'name_en' => null,
            'course_id' => $course->id,
            'memorization_ar' => null,
            'primary_text_ar' => null,
            'supplementary_text_ar' => null,
            'sort_order' => 0,
            'is_required' => true,
            'is_active' => true,
        ]);
    }

    public function test_study_plan_edit_can_move_assignment_to_another_semester(): void
    {
        $this->seed(AcademicStructureSeeder::class);

        $assignment = CurriculumAssignment::query()->with('subject')->firstOrFail();
        $otherSemesterId = Semester::query()
            ->where('id', '!=', $assignment->semester_id)
            ->value('id');

        $this->assertNotNull($otherSemesterId);

        StudyPlanAssignmentForm::saveEdit($assignment, [
            'semester_id' => $otherSemesterId,
            'specialization_id' => null,
            'name_ar' => $assignment->subject?->name_ar,
            'name_en' => null,
            'course_id' => null,
            'memorization_ar' => null,
            'primary_text_ar' => null,
            'supplementary_text_ar' => null,
            'sort_order' => 0,
            'is_required' => true,
            'is_active' => true,
        ]);

        $this->assertSame($otherSemesterId, $assignment->fresh()->semester_id);
    }

    public function test_fill_edit_form_includes_level_year_and_semester(): void
    {
        $this->seed(AcademicStructureSeeder::class);

        $assignment = CurriculumAssignment::query()->with('semester.year')->firstOrFail();

        $data = StudyPlanAssignmentForm::fillEditForm($assignment);

        $this->assertSame($assignment->semester_id, $data['semester_id']);
        $this->assertSame($assignment->semester?->academic_year_id, $data['academic_year_id']);
        $this->assertSame($assignment->semester?->year?->academic_level_id, $data['academic_level_id']);
    }

    public function test_available_course_options_excludes_courses_linked_to_other_subjects(): void
    {
        $this->seed(AcademicStructureSeeder::class);

        $course = Course::query()->create([
            'title_ar' => 'دورة محجوزة',
            'slug' => 'taken-course',
            'is_published' => true,
        ]);

        $assignment = CurriculumAssignment::query()->with('subject')->firstOrFail();
        $assignment->subject?->update(['course_id' => $course->id]);

        $options = StudyPlanAssignmentForm::availableCourseOptions(
            CurriculumAssignment::query()->skip(1)->value('subject_id'),
        );

        $this->assertArrayNotHasKey($course->id, $options);
    }
}
