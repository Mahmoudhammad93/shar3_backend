<?php

namespace Tests\Feature;

use App\Actions\ApproveStudentRegistrationAction;
use App\Actions\RejectStudentRegistrationAction;
use App\Models\AcademicLevel;
use App\Models\AcademicYear;
use App\Models\Course;
use App\Models\Enrollment;
use App\Models\Lesson;
use App\Models\Semester;
use App\Models\Student;
use App\Models\User;
use Database\Seeders\AcademicStructureSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use InvalidArgumentException;
use Tests\TestCase;

class StudentRegistrationOnboardingTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed(AcademicStructureSeeder::class);
    }

    public function test_new_registration_assigns_preparatory_first_year_automatically(): void
    {
        $response = $this->postJson('/api/v1/auth/register', [
            'first_name' => 'أحمد',
            'last_name' => 'محمد',
            'email' => 'auto@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
            'accept_terms' => true,
        ]);

        $response->assertCreated()
            ->assertJsonPath('user.student.status', 'active')
            ->assertJsonPath('user.student.academic_level.slug', 'preparatory-level')
            ->assertJsonPath('user.student.academic_year.slug', 'first-year');

        $student = Student::query()->where('email', 'auto@example.com')->firstOrFail();

        $this->assertSame(Student::STATUS_ACTIVE, $student->status);
        $this->assertNotNull($student->academic_level_id);
        $this->assertNotNull($student->academic_year_id);
        $this->assertNotNull($student->current_semester_id);
    }

    public function test_newly_registered_student_can_access_curriculum(): void
    {
        $response = $this->postJson('/api/v1/auth/register', [
            'first_name' => 'أحمد',
            'last_name' => 'محمد',
            'email' => 'curriculum@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
            'accept_terms' => true,
        ])->assertCreated();

        $this->withToken($response->json('token'))
            ->getJson('/api/v1/student/curriculum')
            ->assertOk();
    }

    public function test_pending_student_cannot_access_academic_dashboard(): void
    {
        $student = $this->createPendingStudent();
        $this->actingAsStudent($student);

        $this->getJson('/api/v1/student/dashboard')
            ->assertForbidden()
            ->assertJsonPath('status', 'pending');
    }

    public function test_pending_student_cannot_access_academic_curriculum(): void
    {
        $this->actingAsStudent($this->createPendingStudent());

        $this->getJson('/api/v1/student/curriculum')
            ->assertForbidden()
            ->assertJsonPath('status', 'pending');
    }

    public function test_pending_student_cannot_access_lessons(): void
    {
        $student = $this->createPendingStudent();
        $this->actingAsStudent($student);

        $course = Course::query()->create([
            'title_ar' => 'دورة',
            'slug' => 'demo-lesson-course',
            'is_published' => true,
        ]);

        $lesson = Lesson::query()->create([
            'course_id' => $course->id,
            'title_ar' => 'درس',
            'slug' => 'lesson-1',
            'sort_order' => 1,
            'is_published' => true,
        ]);

        Enrollment::query()->create([
            'student_id' => $student->id,
            'course_id' => $course->id,
            'status' => Enrollment::STATUS_APPROVED,
        ]);

        $this->postJson("/api/v1/student/lessons/{$lesson->id}/complete")
            ->assertForbidden()
            ->assertJsonPath('status', 'pending');
    }

    public function test_pending_student_cannot_submit_quizzes(): void
    {
        $student = $this->createPendingStudent();
        $this->actingAsStudent($student);

        $course = Course::query()->create([
            'title_ar' => 'دورة',
            'slug' => 'demo-quiz-course',
            'is_published' => true,
        ]);

        $lesson = Lesson::query()->create([
            'course_id' => $course->id,
            'title_ar' => 'درس',
            'slug' => 'quiz-lesson',
            'sort_order' => 1,
            'is_published' => true,
        ]);

        $this->postJson("/api/v1/student/lessons/{$lesson->id}/quiz", [
            'answers' => [],
        ])->assertForbidden()
            ->assertJsonPath('status', 'pending');
    }

    public function test_pending_student_can_access_profile(): void
    {
        $this->actingAsStudent($this->createPendingStudent());

        $this->getJson('/api/v1/student/profile')
            ->assertOk()
            ->assertJsonPath('student.status', Student::STATUS_PENDING);
    }

    public function test_admin_can_approve_pending_student(): void
    {
        $student = $this->createPendingStudent();
        $admin = User::factory()->admin()->create();

        app(ApproveStudentRegistrationAction::class)->execute($student, $admin);

        $student->refresh();

        $this->assertSame(Student::STATUS_ACTIVE, $student->status);
        $this->assertNotNull($student->approved_at);
        $this->assertSame($admin->id, $student->approved_by);
    }

    public function test_approval_assigns_preparatory_level_first_year_and_current_semester(): void
    {
        $student = $this->createPendingStudent();
        $admin = User::factory()->admin()->create();

        app(ApproveStudentRegistrationAction::class)->execute($student, $admin);

        $student->refresh()->load(['academicLevel', 'academicYear', 'currentSemester']);

        $this->assertSame('preparatory-level', $student->academicLevel?->slug);
        $this->assertSame('first-year', $student->academicYear?->slug);
        $this->assertSame(1, $student->currentSemester?->semester_number);
    }

    public function test_approval_exposes_first_year_curriculum_for_current_semester(): void
    {
        $student = $this->createPendingStudent();
        $admin = User::factory()->admin()->create();

        app(ApproveStudentRegistrationAction::class)->execute($student, $admin);

        $this->actingAsStudent($student->fresh());

        $response = $this->getJson('/api/v1/student/curriculum');

        $response->assertOk();

        $slugs = collect($response->json('subjects'))->pluck('slug');

        $this->assertTrue($slugs->contains('y1s1-fiqh'));
        $this->assertFalse($slugs->contains('y1s2-fiqh'));
    }

    public function test_approval_is_atomic_and_rolls_back_on_failure(): void
    {
        $student = $this->createPendingStudent();
        $admin = User::factory()->admin()->create();

        AcademicYear::query()->where('slug', 'first-year')->delete();

        try {
            app(ApproveStudentRegistrationAction::class)->execute($student, $admin);
            $this->fail('Expected approval to fail.');
        } catch (\Throwable) {
            // expected
        }

        $student->refresh();

        $this->assertSame(Student::STATUS_PENDING, $student->status);
        $this->assertNull($student->academic_level_id);
        $this->assertNull($student->academic_year_id);
        $this->assertNull($student->current_semester_id);
        $this->assertNull($student->approved_at);
    }

    public function test_admin_can_reject_registration(): void
    {
        $student = $this->createPendingStudent();
        $admin = User::factory()->admin()->create();

        app(RejectStudentRegistrationAction::class)->execute(
            $student,
            $admin,
            'بيانات غير مكتملة'
        );

        $student->refresh();

        $this->assertSame(Student::STATUS_REJECTED, $student->status);
        $this->assertSame('بيانات غير مكتملة', $student->rejection_reason);
        $this->assertNull($student->academic_level_id);
    }

    public function test_rejected_student_cannot_access_academic_content(): void
    {
        $student = $this->createStudent([
            'status' => Student::STATUS_REJECTED,
            'rejection_reason' => 'مرفوض',
            'academic_level_id' => null,
            'academic_year_id' => null,
            'current_semester_id' => null,
        ]);

        $this->actingAsStudent($student);

        $this->getJson('/api/v1/student/dashboard')
            ->assertForbidden()
            ->assertJsonPath('status', 'rejected');
    }

    public function test_general_course_enrollment_remains_independent_from_academic_enrollment(): void
    {
        $level = AcademicLevel::query()->where('slug', 'preparatory-level')->firstOrFail();
        $year = AcademicYear::query()->where('slug', 'first-year')->firstOrFail();
        $semester = Semester::query()->where('academic_year_id', $year->id)->orderBy('semester_number')->firstOrFail();

        $student = $this->createStudent([
            'academic_level_id' => $level->id,
            'academic_year_id' => $year->id,
            'current_semester_id' => $semester->id,
        ]);

        $course = Course::query()->create([
            'title_ar' => 'دورة عامة',
            'slug' => 'general-course',
            'is_published' => true,
        ]);

        $this->actingAsStudent($student);

        $this->postJson('/api/v1/student/enrollments', [
            'course_id' => $course->id,
        ])->assertCreated();

        $student->refresh();

        $this->assertSame($level->id, $student->academic_level_id);
        $this->assertSame($year->id, $student->academic_year_id);
        $this->assertDatabaseHas('enrollments', [
            'student_id' => $student->id,
            'course_id' => $course->id,
            'status' => Enrollment::STATUS_PENDING,
        ]);
    }

    public function test_pending_student_cannot_enroll_in_general_courses(): void
    {
        $this->actingAsStudent($this->createPendingStudent());

        $course = Course::query()->create([
            'title_ar' => 'دورة عامة',
            'slug' => 'blocked-course',
            'is_published' => true,
        ]);

        $this->postJson('/api/v1/student/enrollments', [
            'course_id' => $course->id,
        ])->assertForbidden()
            ->assertJsonPath('status', 'pending');
    }

    public function test_cannot_approve_non_pending_student(): void
    {
        $student = $this->createStudent();
        $admin = User::factory()->admin()->create();

        $this->expectException(InvalidArgumentException::class);

        app(ApproveStudentRegistrationAction::class)->execute($student, $admin);
    }

    public function test_me_endpoint_returns_authoritative_student_status(): void
    {
        $student = $this->createPendingStudent();
        $this->actingAsStudent($student);

        $this->getJson('/api/v1/auth/me')
            ->assertOk()
            ->assertJsonPath('user.student.status', 'pending')
            ->assertJsonPath('user.student.academic_level', null);
    }
}
