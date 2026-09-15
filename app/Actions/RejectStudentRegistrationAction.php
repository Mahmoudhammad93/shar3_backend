<?php

namespace App\Actions;

use App\Models\Student;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use InvalidArgumentException;

class RejectStudentRegistrationAction
{
    public function execute(Student $student, User $admin, string $reason): Student
    {
        if ($student->status !== Student::STATUS_PENDING) {
            throw new InvalidArgumentException('Only pending students can be rejected.');
        }

        return DB::transaction(function () use ($student, $admin, $reason) {
            $student->update([
                'status' => Student::STATUS_REJECTED,
                'academic_level_id' => null,
                'academic_year_id' => null,
                'current_semester_id' => null,
                'rejected_at' => now(),
                'rejected_by' => $admin->id,
                'rejection_reason' => $reason,
            ]);

            return $student->fresh();
        });
    }
}
