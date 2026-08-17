<?php

namespace App\Actions;

use App\Models\Course;
use App\Models\Enrollment;
use App\Models\Student;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\DB;

class EnrollStudentAction
{
    /**
     * @return array{enrollment: Enrollment, message: string, status_code: int, created: bool}
     */
    public function execute(Student $student, Course $course, ?string $notes = null): array
    {
        return DB::transaction(function () use ($student, $course, $notes) {
            $existing = Enrollment::query()
                ->where('student_id', $student->id)
                ->where('course_id', $course->id)
                ->lockForUpdate()
                ->first();

            if ($existing) {
                return $this->handleExisting($existing, $notes);
            }

            try {
                $enrollment = Enrollment::query()->create([
                    'student_id' => $student->id,
                    'course_id' => $course->id,
                    'status' => Enrollment::STATUS_PENDING,
                    'notes' => $notes,
                ]);
            } catch (QueryException $exception) {
                if (! $this->isDuplicateEnrollmentException($exception)) {
                    throw $exception;
                }

                $enrollment = Enrollment::query()
                    ->where('student_id', $student->id)
                    ->where('course_id', $course->id)
                    ->firstOrFail();

                return $this->handleExisting($enrollment, $notes);
            }

            return [
                'enrollment' => $enrollment,
                'message' => 'تم استلام طلب التسجيل بنجاح. سيتم مراجعته من قبل الإدارة.',
                'status_code' => 201,
                'created' => true,
            ];
        });
    }

    /**
     * @return array{enrollment: Enrollment, message: string, status_code: int, created: bool}
     */
    private function handleExisting(Enrollment $existing, ?string $notes): array
    {
        if ($existing->status === Enrollment::STATUS_REJECTED) {
            $existing->update([
                'status' => Enrollment::STATUS_PENDING,
                'enrolled_at' => null,
                'completed_at' => null,
                'notes' => $notes ?? $existing->notes,
            ]);

            return [
                'enrollment' => $existing->fresh(),
                'message' => 'تم إرسال طلب التسجيل مجدداً وهو قيد المراجعة.',
                'status_code' => 200,
                'created' => false,
            ];
        }

        $message = match ($existing->status) {
            Enrollment::STATUS_PENDING => 'طلب التسجيل قيد المراجعة.',
            Enrollment::STATUS_APPROVED, Enrollment::STATUS_COMPLETED => 'أنت مسجّل بالفعل في هذه الدورة.',
            default => 'تم إرسال طلب التسجيل مسبقاً.',
        };

        $statusCode = in_array($existing->status, [Enrollment::STATUS_APPROVED, Enrollment::STATUS_COMPLETED], true)
            ? 200
            : 202;

        return [
            'enrollment' => $existing,
            'message' => $message,
            'status_code' => $statusCode,
            'created' => false,
        ];
    }

    private function isDuplicateEnrollmentException(QueryException $exception): bool
    {
        $sqlState = $exception->errorInfo[0] ?? null;

        return in_array($sqlState, ['23000', '23505', '19'], true)
            || str_contains(strtolower($exception->getMessage()), 'unique');
    }
}
