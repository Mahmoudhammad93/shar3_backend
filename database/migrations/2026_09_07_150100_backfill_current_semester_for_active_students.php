<?php

use App\Models\Semester;
use App\Models\Student;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    public function up(): void
    {
        Student::query()
            ->where('status', Student::STATUS_ACTIVE)
            ->whereNotNull('academic_year_id')
            ->whereNull('current_semester_id')
            ->each(function (Student $student): void {
                $semester = Semester::query()
                    ->where('academic_year_id', $student->academic_year_id)
                    ->where('is_active', true)
                    ->orderBy('semester_number')
                    ->first();

                if ($semester) {
                    $student->update(['current_semester_id' => $semester->id]);
                }
            });
    }

    public function down(): void
    {
        // Non-destructive data backfill — no rollback required.
    }
};
