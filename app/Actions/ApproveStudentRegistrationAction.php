<?php

namespace App\Actions;

use App\Models\Student;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use InvalidArgumentException;

class ApproveStudentRegistrationAction
{
    public function __construct(
        private readonly AssignPreparatoryPlacementAction $assignPreparatoryPlacement,
    ) {}

    public function execute(Student $student, User $admin): Student
    {
        if ($student->status !== Student::STATUS_PENDING) {
            throw new InvalidArgumentException('Only pending students can be approved.');
        }

        return DB::transaction(function () use ($student, $admin) {
            $student->update([
                'status' => Student::STATUS_ACTIVE,
                ...$this->assignPreparatoryPlacement->execute(),
                'approved_at' => now(),
                'approved_by' => $admin->id,
                'rejected_at' => null,
                'rejected_by' => null,
                'rejection_reason' => null,
            ]);

            return $student->fresh([
                'academicLevel',
                'academicYear',
                'currentSemester',
                'approvedBy',
            ]);
        });
    }
}
