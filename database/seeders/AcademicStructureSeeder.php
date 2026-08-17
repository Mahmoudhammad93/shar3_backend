<?php

namespace Database\Seeders;

use App\Enums\CurriculumType;
use App\Models\AcademicLevel;
use App\Models\AcademicYear;
use App\Models\Course;
use App\Models\CurriculumAssignment;
use App\Models\Semester;
use App\Models\Specialization;
use App\Models\Subject;
use Illuminate\Database\Seeder;

class AcademicStructureSeeder extends Seeder
{
    public function run(): void
    {
        $preparatory = AcademicLevel::query()->updateOrCreate(
            ['number' => 1],
            [
                'name_ar' => 'المستوى التمهيدي',
                'name_en' => 'Preparatory Level',
                'slug' => 'preparatory-level',
                'curriculum_type' => CurriculumType::General,
                'description_ar' => 'السنة الأولى — مواد تأسيسية عامة.',
                'sort_order' => 1,
                'is_active' => true,
            ]
        );

        $advanced = AcademicLevel::query()->updateOrCreate(
            ['number' => 2],
            [
                'name_ar' => 'المستوى المتقدم',
                'name_en' => 'Advanced Level',
                'slug' => 'advanced-level',
                'curriculum_type' => CurriculumType::General,
                'description_ar' => 'السنة الثانية والثالثة — منهج عام.',
                'sort_order' => 2,
                'is_active' => true,
            ]
        );

        $specialized = AcademicLevel::query()->updateOrCreate(
            ['number' => 3],
            [
                'name_ar' => 'المستوى المتخصص',
                'name_en' => 'Specialized Level',
                'slug' => 'specialized-level',
                'curriculum_type' => CurriculumType::Specialized,
                'description_ar' => 'السنة الرابعة والخامسة — اختيار التخصصات.',
                'sort_order' => 3,
                'is_active' => true,
            ]
        );

        $years = [
            [$preparatory, ['year_number' => 1, 'name_ar' => 'السنة الأولى', 'slug' => 'first-year']],
            [$advanced, ['year_number' => 2, 'name_ar' => 'السنة الثانية', 'slug' => 'second-year']],
            [$advanced, ['year_number' => 3, 'name_ar' => 'السنة الثالثة', 'slug' => 'third-year']],
            [$specialized, ['year_number' => 4, 'name_ar' => 'السنة الرابعة', 'slug' => 'fourth-year']],
            [$specialized, ['year_number' => 5, 'name_ar' => 'السنة الخامسة', 'slug' => 'fifth-year']],
        ];

        foreach ($years as $index => [$level, $yearData]) {
            $year = AcademicYear::query()->updateOrCreate(
                ['academic_level_id' => $level->id, 'slug' => $yearData['slug']],
                [
                    ...$yearData,
                    'name_en' => $yearData['name_ar'],
                    'academic_level_id' => $level->id,
                    'sort_order' => $yearData['year_number'],
                    'is_active' => true,
                ]
            );

            $this->seedSemestersForYear($year);
        }

        $specializations = [
            ['slug' => 'fiqh-tafsir', 'name_ar' => 'الفقه والتفسير', 'name_en' => 'Fiqh & Tafsir'],
            ['slug' => 'hadith', 'name_ar' => 'الحديث', 'name_en' => 'Hadith'],
            ['slug' => 'aqeedah', 'name_ar' => 'العقيدة', 'name_en' => 'Aqeedah'],
        ];

        foreach ($specializations as $index => $specData) {
            Specialization::query()->updateOrCreate(
                ['slug' => $specData['slug']],
                [
                    ...$specData,
                    'academic_level_id' => $specialized->id,
                    'sort_order' => $index + 1,
                    'is_active' => true,
                ]
            );
        }

        $this->seedFirstYearGeneralCurriculum();
        $this->seedSpecializedCurriculumSamples();
        $this->linkExistingCoursesToSubjects();
    }

    private function seedFirstYearGeneralCurriculum(): void
    {
        $firstYear = AcademicYear::query()->where('slug', 'first-year')->first();
        $firstSemester = $firstYear?->semesters()->where('semester_number', 1)->first();

        if (! $firstSemester) {
            return;
        }

        $catalog = [
            ['slug' => 'fiqh-1', 'name_ar' => 'الفقه', 'sort_order' => 1],
            ['slug' => 'usul-al-fiqh', 'name_ar' => 'أصول الفقه', 'sort_order' => 2],
            ['slug' => 'nahw', 'name_ar' => 'النحو', 'sort_order' => 3],
            ['slug' => 'mantiq', 'name_ar' => 'المنطق', 'sort_order' => 4],
            ['slug' => 'adab', 'name_ar' => 'الآداب', 'sort_order' => 5],
        ];

        foreach ($catalog as $item) {
            $subject = Subject::query()->updateOrCreate(
                ['slug' => $item['slug']],
                [
                    'name_ar' => $item['name_ar'],
                    'is_active' => true,
                ]
            );

            CurriculumAssignment::query()->updateOrCreate(
                [
                    'semester_id' => $firstSemester->id,
                    'subject_id' => $subject->id,
                    'specialization_id' => null,
                ],
                [
                    'sort_order' => $item['sort_order'],
                    'is_required' => true,
                    'is_active' => true,
                ]
            );
        }
    }

    private function seedSpecializedCurriculumSamples(): void
    {
        $fourthYear = AcademicYear::query()->where('slug', 'fourth-year')->first();
        $firstSemester = $fourthYear?->semesters()->where('semester_number', 1)->first();
        $fiqhSpec = Specialization::query()->where('slug', 'fiqh-tafsir')->first();
        $hadithSpec = Specialization::query()->where('slug', 'hadith')->first();

        if (! $firstSemester || ! $fiqhSpec || ! $hadithSpec) {
            return;
        }

        $algorithms = Subject::query()->updateOrCreate(
            ['slug' => 'algorithms'],
            ['name_ar' => 'Algorithms', 'is_active' => true]
        );

        $fiqhSubject = Subject::query()->updateOrCreate(
            ['slug' => 'fiqh-specialized'],
            ['name_ar' => 'فقه متخصص', 'is_active' => true]
        );

        $hadithSubject = Subject::query()->updateOrCreate(
            ['slug' => 'hadith-specialized'],
            ['name_ar' => 'حديث متخصص', 'is_active' => true]
        );

        foreach ([
            [$fiqhSpec, $fiqhSubject, 1],
            [$fiqhSpec, $algorithms, 2],
            [$hadithSpec, $hadithSubject, 1],
            [$hadithSpec, $algorithms, 2],
        ] as [$spec, $subject, $order]) {
            CurriculumAssignment::query()->updateOrCreate(
                [
                    'semester_id' => $firstSemester->id,
                    'subject_id' => $subject->id,
                    'specialization_id' => $spec->id,
                ],
                [
                    'sort_order' => $order,
                    'is_required' => true,
                    'is_active' => true,
                ]
            );
        }
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
                    'slug' => 'semester-'.$semesterNumber,
                    'sort_order' => $semesterNumber,
                    'is_active' => true,
                ]
            );
        }
    }

    private function linkExistingCoursesToSubjects(): void
    {
        $firstYear = AcademicYear::query()->where('slug', 'first-year')->first();
        $firstSemester = $firstYear?->semesters()->where('semester_number', 1)->first();

        if (! $firstSemester) {
            return;
        }

        $courses = Course::query()->where('is_published', true)->orderBy('sort_order')->get();

        foreach ($courses as $index => $course) {
            $subject = Subject::query()->updateOrCreate(
                ['slug' => 'course-'.$course->slug],
                [
                    'name_ar' => $course->title_ar,
                    'name_en' => $course->title_en,
                    'description_ar' => $course->description_ar,
                    'course_id' => $course->id,
                    'is_active' => true,
                ]
            );

            CurriculumAssignment::query()->updateOrCreate(
                [
                    'semester_id' => $firstSemester->id,
                    'subject_id' => $subject->id,
                    'specialization_id' => null,
                ],
                [
                    'sort_order' => 100 + $index,
                    'is_required' => true,
                    'is_active' => true,
                ]
            );
        }
    }
}
