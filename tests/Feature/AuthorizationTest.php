<?php

namespace Tests\Feature;

use App\Models\Course;
use App\Models\User;
use Filament\Facades\Filament;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class AuthorizationTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_cannot_access_student_dashboard(): void
    {
        $admin = User::factory()->admin()->create();
        Sanctum::actingAs($admin);

        $this->getJson('/api/v1/student/dashboard')->assertForbidden();
    }

    public function test_staff_cannot_access_student_portal(): void
    {
        $staff = User::factory()->create(['role' => 'staff']);
        Sanctum::actingAs($staff);

        $this->getJson('/api/v1/student/courses')->assertForbidden();
    }

    public function test_student_cannot_access_unenrolled_course_details(): void
    {
        $this->actingAsStudent();

        $course = Course::query()->create([
            'title_ar' => 'دورة',
            'slug' => 'locked-course',
            'is_published' => true,
        ]);

        $this->getJson("/api/v1/student/courses/{$course->id}")->assertNotFound();
    }

    public function test_student_user_cannot_access_panel(): void
    {
        $student = $this->createStudent();

        $this->assertFalse($student->user->canAccessPanel(Filament::getPanel('admin')));
    }

    public function test_admin_user_can_access_panel(): void
    {
        $admin = User::factory()->admin()->create();

        $this->assertTrue($admin->canAccessPanel(Filament::getPanel('admin')));
    }
}
