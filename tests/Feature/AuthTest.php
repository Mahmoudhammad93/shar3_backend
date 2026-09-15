<?php

namespace Tests\Feature;

use App\Models\Student;
use App\Models\User;
use Database\Seeders\AcademicStructureSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AuthTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed(AcademicStructureSeeder::class);
    }

    public function test_student_can_register(): void
    {
        $response = $this->postJson('/api/v1/auth/register', [
            'first_name' => 'أحمد',
            'last_name' => 'محمد',
            'email' => 'student@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
            'accept_terms' => true,
        ]);

        $response->assertCreated()
            ->assertJsonStructure(['token', 'user' => ['id', 'email', 'role', 'student']])
            ->assertJsonPath('user.student.status', 'active')
            ->assertJsonPath('user.student.academic_level.slug', 'preparatory-level')
            ->assertJsonPath('user.student.academic_year.slug', 'first-year');

        $this->assertDatabaseHas('users', ['email' => 'student@example.com', 'role' => 'student']);
        $this->assertDatabaseHas('students', [
            'email' => 'student@example.com',
            'status' => Student::STATUS_ACTIVE,
        ]);

        $student = Student::query()->where('email', 'student@example.com')->firstOrFail();
        $this->assertNotNull($student->academic_level_id);
        $this->assertNotNull($student->academic_year_id);
        $this->assertNotNull($student->current_semester_id);
    }

    public function test_newly_registered_student_can_access_dashboard(): void
    {
        $response = $this->postJson('/api/v1/auth/register', [
            'first_name' => 'أحمد',
            'last_name' => 'محمد',
            'email' => 'new-student@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
            'accept_terms' => true,
        ])->assertCreated();

        $token = $response->json('token');

        $this->withToken($token)
            ->getJson('/api/v1/student/dashboard')
            ->assertOk();
    }

    public function test_registration_rejects_duplicate_email(): void
    {
        User::factory()->create(['email' => 'student@example.com']);

        $this->postJson('/api/v1/auth/register', [
            'first_name' => 'أحمد',
            'last_name' => 'محمد',
            'email' => 'student@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
            'accept_terms' => true,
        ])->assertStatus(422);
    }

    public function test_student_can_login(): void
    {
        $student = $this->createStudent(['email' => 'student@example.com']);
        $student->user->update(['password' => 'password123']);

        $this->postJson('/api/v1/auth/login', [
            'email' => 'student@example.com',
            'password' => 'password123',
        ])->assertOk()->assertJsonStructure(['token']);
    }

    public function test_login_fails_with_invalid_credentials(): void
    {
        $this->createStudent(['email' => 'student@example.com']);

        $this->postJson('/api/v1/auth/login', [
            'email' => 'student@example.com',
            'password' => 'wrong-password',
        ])->assertUnauthorized();
    }

    public function test_admin_cannot_login_via_student_api(): void
    {
        User::factory()->admin()->create([
            'email' => 'admin@example.com',
            'password' => 'password123',
        ]);

        $this->postJson('/api/v1/auth/login', [
            'email' => 'admin@example.com',
            'password' => 'password123',
        ])->assertForbidden();
    }

    public function test_authenticated_student_can_access_me(): void
    {
        $student = $this->actingAsStudent();

        $this->getJson('/api/v1/auth/me')
            ->assertOk()
            ->assertJsonPath('user.student_id', $student->id);
    }

    public function test_student_can_logout(): void
    {
        $this->actingAsStudent();

        $this->postJson('/api/v1/auth/logout')->assertOk();
    }

    public function test_pending_student_cannot_access_portal(): void
    {
        $student = $this->createPendingStudent();
        $this->actingAsStudent($student);

        $this->getJson('/api/v1/student/dashboard')->assertForbidden();
    }

    public function test_suspended_student_cannot_access_portal(): void
    {
        $student = $this->createStudent(['status' => Student::STATUS_SUSPENDED]);
        $this->actingAsStudent($student);

        $this->getJson('/api/v1/student/dashboard')->assertForbidden();
    }
}
