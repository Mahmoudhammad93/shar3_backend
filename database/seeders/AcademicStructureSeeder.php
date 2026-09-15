<?php

namespace Database\Seeders;

use App\Enums\CurriculumType;
use App\Enums\StudentSpecializationStatus;
use App\Models\AcademicLevel;
use App\Models\AcademicYear;
use App\Models\CurriculumAssignment;
use App\Models\Program;
use App\Models\Semester;
use App\Models\SiteSetting;
use App\Models\Specialization;
use App\Models\Student;
use App\Models\Subject;
use App\Models\User;
use Illuminate\Database\Seeder;

class AcademicStructureSeeder extends Seeder
{
    /** @var array<string, mixed> */
    private array $plan;

    public function run(): void
    {
        $this->plan = require database_path('seeders/data/study_plan_2026.php');

        CurriculumAssignment::query()->update(['is_active' => false]);

        $this->deactivateLegacySpecializations();
        $this->seedLevelsAndYears();
        $this->seedSpecializations();
        $this->seedGeneralCurriculum();
        $this->seedSpecializedCurriculum();
        $this->seedPrograms();
        $this->seedStudyPlanIntro();
        $this->seedSpecializedStudentDemo();
    }

    private function deactivateLegacySpecializations(): void
    {
        $legacySlugs = $this->plan['legacy_specialization_slugs'] ?? [];

        if ($legacySlugs === []) {
            return;
        }

        $legacyIds = Specialization::query()->whereIn('slug', $legacySlugs)->pluck('id');

        CurriculumAssignment::query()
            ->whereIn('specialization_id', $legacyIds)
            ->update(['is_active' => false]);

        Specialization::query()
            ->whereIn('slug', $legacySlugs)
            ->update(['is_active' => false]);
    }

    private function seedLevelsAndYears(): void
    {
        $yearMap = [
            1 => ['slug' => 'first-year', 'name_ar' => 'السنة الأولى — التمهيدية'],
            2 => ['slug' => 'second-year', 'name_ar' => 'السنة الثانية — التأصيل الأولى'],
            3 => ['slug' => 'third-year', 'name_ar' => 'السنة الثالثة — التأصيل الثانية'],
            4 => ['slug' => 'fourth-year', 'name_ar' => 'السنة الرابعة — التخصص الأولى'],
            5 => ['slug' => 'fifth-year', 'name_ar' => 'السنة الخامسة — التخصص الثانية'],
        ];

        $levelYearNumbers = [
            1 => [1],
            2 => [2, 3],
            3 => [4, 5],
        ];

        foreach ($this->plan['levels'] as $number => $levelData) {
            $level = AcademicLevel::query()->updateOrCreate(
                ['number' => $number],
                [
                    'name_ar' => $levelData['name_ar'],
                    'name_en' => $levelData['name_ar'],
                    'slug' => $levelData['slug'],
                    'curriculum_type' => (int) $number === 3 ? CurriculumType::Specialized : CurriculumType::General,
                    'description_ar' => $levelData['description_ar'],
                    'sort_order' => $number,
                    'is_active' => true,
                ]
            );

            $this->forceSlug($level, $levelData['slug']);

            foreach ($levelYearNumbers[$number] as $yearNumber) {
                $yearData = $yearMap[$yearNumber];

                $year = AcademicYear::query()->updateOrCreate(
                    ['academic_level_id' => $level->id, 'year_number' => $yearNumber],
                    [
                        'name_ar' => $yearData['name_ar'],
                        'name_en' => $yearData['name_ar'],
                        'slug' => $yearData['slug'],
                        'sort_order' => $yearNumber,
                        'is_active' => true,
                    ]
                );

                $this->forceSlug($year, $yearData['slug']);

                $this->seedSemestersForYear($year);
            }
        }
    }

    private function seedSpecializations(): void
    {
        $level = AcademicLevel::query()->where('slug', 'specialized-level')->firstOrFail();

        foreach ($this->plan['specializations'] as $spec) {
            $specialization = Specialization::query()->updateOrCreate(
                ['slug' => $spec['slug']],
                [
                    'academic_level_id' => $level->id,
                    'name_ar' => $spec['name_ar'],
                    'name_en' => $spec['name_en'],
                    'description_ar' => "منهج {$spec['name_ar']} للسنة الرابعة والخامسة.",
                    'sort_order' => $spec['sort'],
                    'is_active' => true,
                ]
            );

            $this->forceSlug($specialization, $spec['slug']);
        }
    }

    private function seedGeneralCurriculum(): void
    {
        foreach ($this->plan['general'] as $yearSlug => $semesters) {
            $year = AcademicYear::query()->where('slug', $yearSlug)->firstOrFail();

            foreach ($semesters as $semesterNumber => $subjects) {
                $this->assignGeneral($year, $semesterNumber, $subjects);
            }
        }
    }

    private function seedSpecializedCurriculum(): void
    {
        $sharedSubjects = collect($this->plan['shared_specialized'] ?? [])
            ->map(fn (array $item): Subject => $this->subject($item['slug'], $item['name_ar'], $item))
            ->all();

        foreach ($this->plan['specialized'] as $specSlug => $years) {
            $specialization = Specialization::query()->where('slug', $specSlug)->firstOrFail();

            foreach ($years as $yearSlug => $semesters) {
                $year = AcademicYear::query()->where('slug', $yearSlug)->firstOrFail();

                foreach ($semesters as $semesterNumber => $subjects) {
                    $items = $subjects;

                    if ($semesterNumber === 1 && $sharedSubjects !== []) {
                        $items = array_merge($items, $sharedSubjects);
                    }

                    $this->assignSpecialized($specialization, $year, $semesterNumber, $items);
                }
            }
        }
    }

    private function seedPrograms(): void
    {
        $activeSlugs = [];

        foreach ($this->plan['programs'] as $programData) {
            $sortOrder = $programData['sort_order'];
            $slug = $programData['slug'];

            $program = Program::query()
                ->where(function ($query) use ($sortOrder, $slug): void {
                    $query->where('sort_order', $sortOrder)
                        ->orWhere('slug', $slug);
                })
                ->orderBy('id')
                ->first();

            if (! $program) {
                $program = new Program;
            }

            $program->fill([
                'name_ar' => $programData['name_ar'],
                'name_en' => $programData['name_ar'],
                'slug' => $slug,
                'description_ar' => $programData['description_ar'],
                'duration' => $programData['duration'],
                'level' => $programData['level'],
                'sort_order' => $sortOrder,
                'is_active' => true,
            ]);
            $program->save();

            $this->forceSlug($program, $slug);
            $activeSlugs[] = $slug;
        }

        $legacySlugs = array_diff($this->plan['legacy_program_slugs'] ?? [], $activeSlugs);

        if ($legacySlugs !== []) {
            Program::query()
                ->whereIn('slug', $legacySlugs)
                ->update(['is_active' => false]);
        }
    }

    private function seedStudyPlanIntro(): void
    {
        $settings = SiteSetting::current();
        $settings->study_plan_intro_ar = $this->plan['intro_ar'];
        $settings->save();
    }

    /** @param  array<int, array<string, mixed>|Subject>  $items */
    private function assignGeneral(AcademicYear $year, int $semesterNumber, array $items): void
    {
        $semester = $year->semesters()->where('semester_number', $semesterNumber)->first();

        if (! $semester) {
            return;
        }

        foreach ($items as $index => $item) {
            $subject = $item instanceof Subject
                ? $item
                : $this->subject($item['slug'], $item['name_ar'], collect($item)->except(['slug', 'name_ar'])->all());

            CurriculumAssignment::query()->updateOrCreate(
                [
                    'semester_id' => $semester->id,
                    'subject_id' => $subject->id,
                    'specialization_id' => null,
                ],
                [
                    'sort_order' => $index + 1,
                    'is_required' => true,
                    'is_active' => true,
                ]
            );
        }
    }

    /** @param  array<int, array<string, mixed>|Subject>  $items */
    private function assignSpecialized(
        Specialization $specialization,
        AcademicYear $year,
        int $semesterNumber,
        array $items,
    ): void {
        $semester = $year->semesters()->where('semester_number', $semesterNumber)->first();

        if (! $semester) {
            return;
        }

        foreach ($items as $index => $item) {
            $subject = $item instanceof Subject
                ? $item
                : $this->subject($item['slug'], $item['name_ar'], collect($item)->except(['slug', 'name_ar'])->all());

            CurriculumAssignment::query()->updateOrCreate(
                [
                    'semester_id' => $semester->id,
                    'subject_id' => $subject->id,
                    'specialization_id' => $specialization->id,
                ],
                [
                    'sort_order' => $index + 1,
                    'is_required' => true,
                    'is_active' => true,
                ]
            );
        }
    }

    private function subject(string $slug, string $nameAr, array $extra = []): Subject
    {
        $subject = Subject::query()->updateOrCreate(
            ['slug' => $slug],
            array_merge(['name_ar' => $nameAr, 'is_active' => true], $extra)
        );

        $this->forceSlug($subject, $slug);

        return $subject;
    }

    private function forceSlug(AcademicLevel|AcademicYear|Subject|Program|Specialization $model, string $slug): void
    {
        if ($model->slug === $slug) {
            return;
        }

        $model->slug = $slug;
        $model->saveQuietly();
    }

    private function seedSemestersForYear(AcademicYear $year): void
    {
        foreach ([1, 2] as $semesterNumber) {
            Semester::query()->updateOrCreate(
                [
                    'academic_year_id' => $year->id,
                    'semester_number' => $semesterNumber,
                ],
                [
                    'name_ar' => $semesterNumber === 1 ? 'الفصل الأول' : 'الفصل الثاني',
                    'name_en' => $semesterNumber === 1 ? 'First Semester' : 'Second Semester',
                    'slug' => $year->slug.'-semester-'.$semesterNumber,
                    'sort_order' => $semesterNumber,
                    'is_active' => true,
                ]
            );
        }
    }

    private function seedSpecializedStudentDemo(): void
    {
        $level = AcademicLevel::query()->where('slug', 'specialized-level')->firstOrFail();
        $year = AcademicYear::query()->where('slug', 'fourth-year')->firstOrFail();
        $fiqhSpec = Specialization::query()->where('slug', 'fiqh-usul')->first();
        $hadithSpec = Specialization::query()->where('slug', 'hadith-sciences')->first();

        if (! $fiqhSpec || ! $hadithSpec) {
            return;
        }

        $user = User::query()->firstOrCreate(
            ['email' => 'specialized@share3a.com'],
            ['name' => 'محمود المتخصص', 'password' => 'password', 'role' => 'student']
        );

        $student = Student::query()->updateOrCreate(
            ['email' => 'specialized@share3a.com'],
            [
                'user_id' => $user->id,
                'name' => 'محمود المتخصص',
                'status' => Student::STATUS_ACTIVE,
                'academic_level_id' => $level->id,
                'academic_year_id' => $year->id,
                'terms_accepted_at' => now(),
            ]
        );

        $student->specializations()->sync([
            $fiqhSpec->id => [
                'status' => StudentSpecializationStatus::Active->value,
                'selected_at' => now(),
            ],
            $hadithSpec->id => [
                'status' => StudentSpecializationStatus::Active->value,
                'selected_at' => now(),
            ],
        ]);
    }
}
