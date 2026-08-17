<?php

namespace Tests\Feature;

use App\Models\AcademicLevel;
use App\Models\AcademicYear;
use App\Models\Specialization;
use App\Models\StudentSpecialization;
use App\Models\User;
use Database\Seeders\AcademicStructureSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class StudentSpecializationTest extends TestCase
{
    use RefreshDatabase;

    private function specializedStudentSetup(): array
    {
        $this->seed(AcademicStructureSeeder::class);

        $level = AcademicLevel::query()->where('slug', 'specialized-level')->firstOrFail();
        $year = AcademicYear::query()->where('slug', 'fourth-year')->firstOrFail();

        $student = $this->createStudent([
            'academic_level_id' => $level->id,
            'academic_year_id' => $year->id,
        ]);

        $this->actingAsStudent($student);

        $specs = Specialization::query()->where('academic_level_id', $level->id)->get();

        return [$student, $level, $year, $specs];
    }

    public function test_student_can_select_multiple_specializations(): void
    {
        [, , , $specs] = $this->specializedStudentSetup();

        $this->postJson('/api/v1/student/specializations', [
            'specialization_ids' => $specs->take(2)->pluck('id')->all(),
        ])->assertOk()
            ->assertJsonCount(2, 'specializations');

        $this->assertEquals(2, StudentSpecialization::query()->count());
    }

    public function test_student_can_sync_specializations(): void
    {
        [$student, , , $specs] = $this->specializedStudentSetup();

        $first = $specs->first();
        $second = $specs->skip(1)->first();

        $student->specializations()->attach($first->id, [
            'status' => 'active',
            'selected_at' => now(),
        ]);

        $this->postJson('/api/v1/student/specializations', [
            'specialization_ids' => [$second->id],
        ])->assertOk()
            ->assertJsonCount(1, 'specializations');

        $this->assertFalse($student->specializations()->where('specializations.id', $first->id)->exists());
    }

    public function test_student_cannot_select_inactive_specialization(): void
    {
        [, , , $specs] = $this->specializedStudentSetup();

        $inactive = $specs->first();
        $inactive->update(['is_active' => false]);

        $this->postJson('/api/v1/student/specializations', [
            'specialization_ids' => [$inactive->id],
        ])->assertStatus(422);
    }

    public function test_general_level_student_cannot_select_specializations(): void
    {
        $this->seed(AcademicStructureSeeder::class);

        $level = AcademicLevel::query()->where('slug', 'preparatory-level')->firstOrFail();
        $year = AcademicYear::query()->where('slug', 'first-year')->firstOrFail();
        $spec = Specialization::query()->firstOrFail();

        $this->actingAsStudent($this->createStudent([
            'academic_level_id' => $level->id,
            'academic_year_id' => $year->id,
        ]));

        $this->postJson('/api/v1/student/specializations', [
            'specialization_ids' => [$spec->id],
        ])->assertStatus(422);
    }

    public function test_admin_cannot_modify_student_specializations_via_student_api(): void
    {
        $this->seed(AcademicStructureSeeder::class);

        $admin = User::factory()->admin()->create();
        Sanctum::actingAs($admin);

        $this->postJson('/api/v1/student/specializations', [
            'specialization_ids' => [1],
        ])->assertForbidden();
    }

    public function test_duplicate_specialization_ids_are_rejected(): void
    {
        [, , , $specs] = $this->specializedStudentSetup();
        $id = $specs->first()->id;

        $this->postJson('/api/v1/student/specializations', [
            'specialization_ids' => [$id, $id],
        ])->assertStatus(422);
    }
}
