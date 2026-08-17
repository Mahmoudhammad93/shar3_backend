<?php

namespace Database\Seeders;

use App\Models\AcademicLevel;
use App\Models\AcademicYear;
use App\Models\Course;
use App\Models\Semester;
use App\Models\Specialization;
use App\Models\Subject;
use Illuminate\Database\Seeder;

class AcademicStructureSeeder extends Seeder
{
    public function run(): void
    {
        $level1 = AcademicLevel::query()->updateOrCreate(
            ['number' => 1],
            [
                'name_ar' => 'المستوى الأول',
                'name_en' => 'Level 1',
                'slug' => 'level-1',
                'description_ar' => 'مرحلة التأسيس والسنوات الأولى في العلوم الشرعية.',
                'sort_order' => 1,
                'is_active' => true,
            ]
        );

        $level2 = AcademicLevel::query()->updateOrCreate(
            ['number' => 2],
            [
                'name_ar' => 'المستوى الثاني',
                'name_en' => 'Level 2',
                'slug' => 'level-2',
                'description_ar' => 'مرحلة التخصص في العلوم الشرعية.',
                'sort_order' => 2,
                'is_active' => true,
            ]
        );

        $level1Years = [
            ['year_number' => 0, 'name_ar' => 'السنة التمهيدية', 'name_en' => 'Preparatory Year', 'slug' => 'preparatory'],
            ['year_number' => 1, 'name_ar' => 'السنة الأولى', 'name_en' => 'First Year', 'slug' => 'first-year'],
            ['year_number' => 2, 'name_ar' => 'السنة الثانية', 'name_en' => 'Second Year', 'slug' => 'second-year'],
        ];

        foreach ($level1Years as $index => $yearData) {
            $year = AcademicYear::query()->updateOrCreate(
                ['academic_level_id' => $level1->id, 'slug' => $yearData['slug']],
                [
                    ...$yearData,
                    'academic_level_id' => $level1->id,
                    'sort_order' => $index + 1,
                    'is_active' => true,
                ]
            );

            $this->seedSemestersForYear($year);
        }

        $level2Years = [
            ['year_number' => 4, 'name_ar' => 'السنة الرابعة', 'name_en' => 'Fourth Year', 'slug' => 'fourth-year'],
            ['year_number' => 5, 'name_ar' => 'السنة الخامسة', 'name_en' => 'Fifth Year', 'slug' => 'fifth-year'],
        ];

        foreach ($level2Years as $index => $yearData) {
            $year = AcademicYear::query()->updateOrCreate(
                ['academic_level_id' => $level2->id, 'slug' => $yearData['slug']],
                [
                    ...$yearData,
                    'academic_level_id' => $level2->id,
                    'sort_order' => $index + 1,
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
                    'academic_level_id' => $level2->id,
                    'sort_order' => $index + 1,
                    'is_active' => true,
                ]
            );
        }

        $this->linkExistingCoursesToSubjects();
        $this->seedStudyPlanDetails();
    }

    private function seedStudyPlanDetails(): void
    {
        $firstYear = AcademicYear::query()
            ->where('slug', 'first-year')
            ->whereHas('level', fn ($q) => $q->where('number', 1))
            ->first();

        if (! $firstYear) {
            return;
        }

        $firstSemester = $firstYear->semesters()->where('semester_number', 1)->first();

        if (! $firstSemester) {
            return;
        }

        $subjects = [
            [
                'slug' => 'fiqh-1',
                'name_ar' => 'الفقه',
                'memorization_ar' => "متن \"الآداب الشرعية\" — حتى (من يريد أن يتزوج)\nمتن \"الورقات\" — حتى (وأما المسائل)",
                'primary_text_ar' => "زاد المستقنع — دار المنهاج",
                'supplementary_text_ar' => "الملخص الفقهي — دار المنهاج",
                'sort_order' => 1,
            ],
            [
                'slug' => 'usul-al-fiqh',
                'name_ar' => 'أصول الفقه',
                'memorization_ar' => null,
                'primary_text_ar' => "الواضح في أصول الفقه — دار المنهاج",
                'supplementary_text_ar' => "الورقات — دار المنهاج",
                'sort_order' => 2,
            ],
            [
                'slug' => 'nahw',
                'name_ar' => 'النحو',
                'memorization_ar' => "متن \"الآجرومية\" — حتى (وَالْمُضَافُ إِلَيْهِ)",
                'primary_text_ar' => "الآجرومية — دار المنهاج",
                'supplementary_text_ar' => "قطر الندى — دار المنهاج",
                'sort_order' => 3,
            ],
            [
                'slug' => 'mantiq',
                'name_ar' => 'المنطق',
                'memorization_ar' => null,
                'primary_text_ar' => "الرسالة الشمسية — دار المنهاج",
                'supplementary_text_ar' => null,
                'sort_order' => 4,
            ],
            [
                'slug' => 'adab',
                'name_ar' => 'الآداب',
                'memorization_ar' => null,
                'primary_text_ar' => "الآداب المفرد — دار المنهاج",
                'supplementary_text_ar' => null,
                'sort_order' => 5,
            ],
        ];

        foreach ($subjects as $subjectData) {
            Subject::query()->updateOrCreate(
                [
                    'semester_id' => $firstSemester->id,
                    'slug' => $subjectData['slug'],
                ],
                [
                    ...$subjectData,
                    'semester_id' => $firstSemester->id,
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
        $preparatoryYear = AcademicYear::query()
            ->where('slug', 'preparatory')
            ->whereHas('level', fn ($q) => $q->where('number', 1))
            ->first();

        if (! $preparatoryYear) {
            return;
        }

        $firstSemester = $preparatoryYear->semesters()->where('semester_number', 1)->first();

        if (! $firstSemester) {
            return;
        }

        $courses = Course::query()->where('is_published', true)->orderBy('sort_order')->get();

        foreach ($courses as $index => $course) {
            Subject::query()->updateOrCreate(
                ['course_id' => $course->id],
                [
                    'semester_id' => $firstSemester->id,
                    'name_ar' => $course->title_ar,
                    'name_en' => $course->title_en,
                    'slug' => $course->slug,
                    'description_ar' => $course->description_ar,
                    'description_en' => $course->description_en,
                    'sort_order' => $index + 1,
                    'is_required' => true,
                    'is_active' => true,
                ]
            );
        }
    }
}
