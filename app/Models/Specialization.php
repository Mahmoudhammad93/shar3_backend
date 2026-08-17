<?php

namespace App\Models;

use App\Models\Concerns\HasSlug;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable([
    'academic_level_id', 'name_ar', 'name_en', 'slug',
    'description_ar', 'description_en', 'sort_order', 'is_active',
])]
class Specialization extends Model
{
    use HasSlug;

    protected function casts(): array
    {
        return [
            'sort_order' => 'integer',
            'is_active' => 'boolean',
        ];
    }

    public function level(): BelongsTo
    {
        return $this->belongsTo(AcademicLevel::class, 'academic_level_id');
    }

    public function curriculumAssignments(): HasMany
    {
        return $this->hasMany(CurriculumAssignment::class);
    }

    public function studentChoices(): HasMany
    {
        return $this->hasMany(StudentSpecialization::class);
    }

    public function students(): BelongsToMany
    {
        return $this->belongsToMany(Student::class, 'student_specializations')
            ->using(StudentSpecializationPivot::class)
            ->withPivot(['status', 'selected_at'])
            ->withTimestamps();
    }
}
