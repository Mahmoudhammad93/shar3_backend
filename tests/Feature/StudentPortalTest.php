<?php

namespace Tests\Feature;

use App\Models\Course;
use App\Models\Enrollment;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class StudentPortalTest extends TestCase
{
    use RefreshDatabase;

    public function test_student_can_access_dashboard(): void
    {
        $student = $this->actingAsStudent();

        $course = Course::query()->create([
            'title_ar' => 'دورة',
            'slug' => 'dashboard-course',
            'is_published' => true,
        ]);

        Enrollment::query()->create([
            'student_id' => $student->id,
            'course_id' => $course->id,
            'status' => Enrollment::STATUS_APPROVED,
            'enrolled_at' => now(),
        ]);

        $this->getJson('/api/v1/student/dashboard')
            ->assertOk()
            ->assertJsonStructure([
                'stats' => [
                    'active_courses',
                    'pending_enrollments',
                    'completed_lessons',
                    'total_lessons',
                    'progress_percent',
                    'pending_assignments',
                    'certificates',
                ],
                'recent_courses',
            ])
            ->assertJsonPath('stats.active_courses', 1);
    }

    public function test_student_can_list_enrolled_courses(): void
    {
        $student = $this->actingAsStudent();

        $course = Course::query()->create([
            'title_ar' => 'دورة',
            'slug' => 'student-course',
            'is_published' => true,
        ]);

        Enrollment::query()->create([
            'student_id' => $student->id,
            'course_id' => $course->id,
            'status' => Enrollment::STATUS_APPROVED,
            'enrolled_at' => now(),
        ]);

        $this->getJson('/api/v1/student/courses')
            ->assertOk()
            ->assertJsonCount(1, 'data');
    }

    public function test_student_can_view_profile(): void
    {
        $student = $this->actingAsStudent();

        $this->getJson('/api/v1/student/profile')
            ->assertOk()
            ->assertJsonPath('user.email', $student->email);
    }

    public function test_student_can_view_grades(): void
    {
        $this->actingAsStudent();

        $this->getJson('/api/v1/student/grades')
            ->assertOk()
            ->assertJsonStructure(['grades', 'certificates']);
    }

    public function test_student_can_view_assignments(): void
    {
        $this->actingAsStudent();

        $this->getJson('/api/v1/student/assignments')
            ->assertOk()
            ->assertJsonStructure(['data']);
    }
}
