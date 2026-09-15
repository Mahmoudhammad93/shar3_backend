<?php

namespace App\Actions;

use App\Enums\CurriculumType;
use App\Enums\StudentSpecializationStatus;
use App\Models\CurriculumAssignment;
use App\Models\Student;
use Illuminate\Support\Collection;

class ResolveStudentCurriculumAction
{
    /**
     * @return Collection<int, array<string, mixed>>
     */
    public function forStudent(Student $student): Collection
    {
        $student->loadMissing(['academicLevel', 'academicYear', 'specializations', 'currentSemester']);

        if (! $student->academic_year_id) {
            return collect();
        }

        $assignments = $this->generalAssignmentsForYear($student->academic_year_id, $student->current_semester_id);

        if ($student->academicLevel?->curriculum_type === CurriculumType::Specialized) {
            $specializationIds = $student->specializations()
                ->wherePivot('status', StudentSpecializationStatus::Active->value)
                ->pluck('specializations.id');

            if ($specializationIds->isNotEmpty()) {
                $specialized = CurriculumAssignment::query()
                    ->with([
                        'subject.course:id,slug,title_ar,title_en',
                        'semester.year.level',
                        'specialization:id,name_ar,name_en,slug',
                    ])
                    ->where('is_active', true)
                    ->whereIn('specialization_id', $specializationIds)
                    ->whereHas('semester', fn ($q) => $q
                        ->where('academic_year_id', $student->academic_year_id)
                        ->when($student->current_semester_id, fn ($query) => $query->where('id', $student->current_semester_id))
                        ->where('is_active', true))
                    ->orderBy('sort_order')
                    ->get();

                $assignments = $assignments->concat($specialized);
            }
        }

        return $this->deduplicateBySubject($this->mapAssignments($assignments));
    }

    /**
     * @return Collection<int, CurriculumAssignment>
     */
    private function generalAssignmentsForYear(int $academicYearId, ?int $semesterId = null): Collection
    {
        return CurriculumAssignment::query()
            ->with([
                'subject.course:id,slug,title_ar,title_en',
                'semester.year.level',
                'specialization:id,name_ar,name_en,slug',
            ])
            ->whereNull('specialization_id')
            ->where('is_active', true)
            ->whereHas('semester', fn ($q) => $q
                ->where('academic_year_id', $academicYearId)
                ->when($semesterId, fn ($query) => $query->where('id', $semesterId))
                ->where('is_active', true))
            ->orderBy('sort_order')
            ->get();
    }

    /**
     * @param  Collection<int, CurriculumAssignment>  $assignments
     * @return Collection<int, array<string, mixed>>
     */
    private function mapAssignments(Collection $assignments): Collection
    {
        return $assignments->map(function (CurriculumAssignment $assignment) {
            $subject = $assignment->subject;
            $semester = $assignment->semester;
            $year = $semester?->year;

            return [
                'assignment_id' => $assignment->id,
                'subject_id' => $subject?->id,
                'name_ar' => $subject?->name_ar,
                'name_en' => $subject?->name_en,
                'slug' => $subject?->slug,
                'is_required' => $assignment->is_required,
                'memorization_ar' => $subject?->memorization_ar,
                'primary_text_ar' => $subject?->primary_text_ar,
                'supplementary_text_ar' => $subject?->supplementary_text_ar,
                'specializations' => $assignment->specialization ? [[
                    'id' => $assignment->specialization->id,
                    'name_ar' => $assignment->specialization->name_ar,
                    'slug' => $assignment->specialization->slug,
                ]] : [],
                'academic_year' => $year ? [
                    'id' => $year->id,
                    'name_ar' => $year->name_ar,
                    'year_number' => $year->year_number,
                ] : null,
                'semester' => $semester ? [
                    'id' => $semester->id,
                    'name_ar' => $semester->name_ar,
                    'semester_number' => $semester->semester_number,
                ] : null,
                'course' => $subject?->course ? [
                    'id' => $subject->course->id,
                    'slug' => $subject->course->slug,
                    'title_ar' => $subject->course->title_ar,
                ] : null,
            ];
        })->filter(fn (array $row) => $row['subject_id'] !== null);
    }

    /**
     * @param  Collection<int, array<string, mixed>>  $subjects
     * @return Collection<int, array<string, mixed>>
     */
    private function deduplicateBySubject(Collection $subjects): Collection
    {
        return $subjects
            ->groupBy('subject_id')
            ->map(function (Collection $group) {
                $merged = $group->first();
                $merged['specializations'] = $group
                    ->flatMap(fn (array $row) => $row['specializations'] ?? [])
                    ->unique('id')
                    ->values()
                    ->all();

                return $merged;
            })
            ->values();
    }
}
