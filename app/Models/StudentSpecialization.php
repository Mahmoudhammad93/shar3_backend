<?php

namespace App\Models;

use App\Enums\StudentSpecializationStatus;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable([
    'student_id', 'specialization_id', 'status', 'selected_at',
])]
class StudentSpecialization extends Model
{
    protected function casts(): array
    {
        return [
            'status' => StudentSpecializationStatus::class,
            'selected_at' => 'datetime',
        ];
    }

    public function student(): BelongsTo
    {
        return $this->belongsTo(Student::class);
    }

    public function specialization(): BelongsTo
    {
        return $this->belongsTo(Specialization::class);
    }
}
