<?php

namespace Tests\Feature;

use App\Models\Course;
use App\Models\Enrollment;
use App\Models\Student;
use Illuminate\Database\QueryException;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class EnrollmentTest extends TestCase
{
    use RefreshDatabase;

    private function publishedCourse(): Course
    {
        return Course::query()->create([
            'title_ar' => 'دورة تجريبية',
            'slug' => 'demo-course',
            'is_published' => true,
        ]);
    }

    public function test_student_can_submit_first_enrollment_as_pending(): void
    {
        $student = $this->actingAsStudent();
        $course = $this->publishedCourse();

        $response = $this->postJson('/api/v1/student/enrollments', [
            'course_id' => $course->id,
        ]);

        $response->assertCreated()
            ->assertJsonPath('enrollment.status', Enrollment::STATUS_PENDING);

        $this->assertDatabaseHas('enrollments', [
            'student_id' => $student->id,
            'course_id' => $course->id,
            'status' => Enrollment::STATUS_PENDING,
        ]);
    }

    public function test_duplicate_enrollment_returns_existing_status(): void
    {
        $student = $this->actingAsStudent();
        $course = $this->publishedCourse();

        Enrollment::query()->create([
            'student_id' => $student->id,
            'course_id' => $course->id,
            'status' => Enrollment::STATUS_PENDING,
        ]);

        $this->postJson('/api/v1/student/enrollments', [
            'course_id' => $course->id,
        ])->assertStatus(202)
            ->assertJsonPath('enrollment.status', Enrollment::STATUS_PENDING);

        $this->assertEquals(1, Enrollment::query()->count());
    }

    public function test_rejected_enrollment_can_be_resubmitted_as_pending(): void
    {
        $student = $this->actingAsStudent();
        $course = $this->publishedCourse();

        Enrollment::query()->create([
            'student_id' => $student->id,
            'course_id' => $course->id,
            'status' => Enrollment::STATUS_REJECTED,
        ]);

        $this->postJson('/api/v1/student/enrollments', [
            'course_id' => $course->id,
        ])->assertOk()
            ->assertJsonPath('enrollment.status', Enrollment::STATUS_PENDING);
    }

    public function test_guest_enrollment_is_pending(): void
    {
        $course = $this->publishedCourse();

        $this->postJson('/api/v1/enrollments', [
            'name' => 'زائر',
            'email' => 'guest@example.com',
            'course_id' => $course->id,
        ])->assertCreated()
            ->assertJsonPath('enrollment.status', Enrollment::STATUS_PENDING);
    }

    public function test_unauthenticated_user_cannot_use_student_enrollment_endpoint(): void
    {
        $course = $this->publishedCourse();

        $this->postJson('/api/v1/student/enrollments', [
            'course_id' => $course->id,
        ])->assertUnauthorized();
    }

    public function test_database_enforces_unique_student_course_pair(): void
    {
        $student = Student::query()->create([
            'name' => 'Test',
            'email' => 'unique@example.com',
            'status' => Student::STATUS_ACTIVE,
        ]);

        $course = $this->publishedCourse();

        Enrollment::query()->create([
            'student_id' => $student->id,
            'course_id' => $course->id,
            'status' => Enrollment::STATUS_PENDING,
        ]);

        $this->expectException(QueryException::class);

        Enrollment::query()->create([
            'student_id' => $student->id,
            'course_id' => $course->id,
            'status' => Enrollment::STATUS_PENDING,
        ]);
    }
}
