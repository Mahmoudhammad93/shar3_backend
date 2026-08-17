<?php

namespace App\Models;

use App\Enums\StudentSpecializationStatus;
use Illuminate\Database\Eloquent\Relations\Pivot;

class StudentSpecializationPivot extends Pivot
{
    protected function casts(): array
    {
        return [
            'status' => StudentSpecializationStatus::class,
            'selected_at' => 'datetime',
        ];
    }
}
