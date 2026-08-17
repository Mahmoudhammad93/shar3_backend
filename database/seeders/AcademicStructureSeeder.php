<?php

namespace Database\Seeders;

use App\Enums\CurriculumType;
use App\Enums\StudentSpecializationStatus;
use App\Models\AcademicLevel;
use App\Models\AcademicYear;
use App\Models\Course;
use App\Models\CurriculumAssignment;
use App\Models\Semester;
use App\Models\Specialization;
use App\Models\Student;
use App\Models\Subject;
use App\Models\User;
use Illuminate\Database\Seeder;

class AcademicStructureSeeder extends Seeder
{
    public function run(): void
    {
        $preparatory = $this->level(1, 'المستوى التمهيدي', 'preparatory-level', CurriculumType::General, 'السنة الأولى — مواد تأسيسية عامة.');
        $advanced = $this->level(2, 'المستوى المتقدم', 'advanced-level', CurriculumType::General, 'السنة الثانية والثالثة — منهج عام.');
        $specialized = $this->level(3, 'المستوى المتخصص', 'specialized-level', CurriculumType::Specialized, 'السنة الرابعة والخامسة — اختيار التخصصات.');

        $year1 = $this->year($preparatory, 1, 'السنة الأولى', 'first-year');
        $year2 = $this->year($advanced, 2, 'السنة الثانية', 'second-year');
        $year3 = $this->year($advanced, 3, 'السنة الثالثة', 'third-year');
        $year4 = $this->year($specialized, 4, 'السنة الرابعة', 'fourth-year');
        $year5 = $this->year($specialized, 5, 'السنة الخامسة', 'fifth-year');

        foreach ([$year1, $year2, $year3, $year4, $year5] as $year) {
            $this->seedSemestersForYear($year);
        }

        // ── General curriculum: المستوى التمهيدي — السنة الأولى ──
        $this->assignGeneral($year1, 1, [
            ['slug' => 'fiqh-1', 'name_ar' => 'الفقه', 'primary_text_ar' => 'زاد المستقنع — دار المنهاج'],
            ['slug' => 'usul-al-fiqh', 'name_ar' => 'أصول الفقه', 'primary_text_ar' => 'الواضح في أصول الفقه'],
            ['slug' => 'nahw', 'name_ar' => 'النحو', 'memorization_ar' => 'متن الآجرومية'],
            ['slug' => 'mantiq', 'name_ar' => 'المنطق'],
            ['slug' => 'adab', 'name_ar' => 'الآداب'],
        ]);
        $this->assignGeneral($year1, 2, [
            ['slug' => 'sarf', 'name_ar' => 'الصرف'],
            ['slug' => 'balagha', 'name_ar' => 'البلاغة'],
            ['slug' => 'mustalah-hadith', 'name_ar' => 'مصطلح الحديث'],
        ]);

        // ── General curriculum: المستوى المتقدم — السنة الثانية ──
        $this->assignGeneral($year2, 1, [
            ['slug' => 'fiqh-2', 'name_ar' => 'الفقه — المستوى الثاني'],
            ['slug' => 'tafsir-1', 'name_ar' => 'التفسير'],
            ['slug' => 'hadith-1', 'name_ar' => 'الحديث'],
        ]);
        $this->assignGeneral($year2, 2, [
            ['slug' => 'aqeedah-1', 'name_ar' => 'العقيدة'],
            ['slug' => 'seerah', 'name_ar' => 'السيرة'],
        ]);

        // ── General curriculum: المستوى المتقدم — السنة الثالثة ──
        $this->assignGeneral($year3, 1, [
            ['slug' => 'fiqh-3', 'name_ar' => 'الفقه — المستوى الثالث'],
            ['slug' => 'usul-tafsir', 'name_ar' => 'أصول التفسير'],
        ]);
        $this->assignGeneral($year3, 2, [
            ['slug' => 'qawaid-fiqh', 'name_ar' => 'القواعد الفقهية'],
            ['slug' => 'frq-hadith', 'name_ar' => 'فرق الحديث'],
        ]);

        // ── Specializations (المستوى المتخصص) ──
        $fiqhSpec = $this->specialization($specialized, 'fiqh-tafsir', 'الفقه والتفسير', 'Fiqh & Tafsir', 1, true);
        $hadithSpec = $this->specialization($specialized, 'hadith', 'الحديث', 'Hadith', 2, true);
        $aqeedahSpec = $this->specialization($specialized, 'aqeedah', 'العقيدة', 'Aqeedah', 3, true);
        $this->specialization($specialized, 'usool-deen-archived', 'أصول الدين (متوقف)', 'Usul (inactive)', 4, false);

        // Shared subject across specializations (deduplication test case)
        $researchMethods = $this->subject('research-methods', 'منهجية البحث العلمي');

        // General subjects in specialized years (all students year 4/5)
        $this->assignGeneral($year4, 1, [
            ['slug' => 'general-seminar-y4s1', 'name_ar' => 'ندوة علمية عامة — السنة الرابعة'],
        ]);
        $this->assignGeneral($year4, 2, [
            ['slug' => 'general-project-prep', 'name_ar' => 'إعداد مشروع التخرج'],
        ]);

        // ── الفقه والتفسير curriculum (years 4 & 5) ──
        $this->assignSpecialized($fiqhSpec, $year4, 1, [
            ['slug' => 'fiqh-muamalat', 'name_ar' => 'فقه المعاملات'],
            ['slug' => 'tafsir-juz-amm', 'name_ar' => 'تفسير جزء عم'],
            $researchMethods,
        ]);
        $this->assignSpecialized($fiqhSpec, $year4, 2, [
            ['slug' => 'fiqh-waqf', 'name_ar' => 'فقه الوقف'],
            ['slug' => 'uloom-quran', 'name_ar' => 'علوم القرآن'],
        ]);
        $this->assignSpecialized($fiqhSpec, $year5, 1, [
            ['slug' => 'fiqh-awqaf-advanced', 'name_ar' => 'فقه الأوقاف المتقدم'],
            ['slug' => 'tafsir-themes', 'name_ar' => 'موضوعات التفسير'],
        ]);
        $this->assignSpecialized($fiqhSpec, $year5, 2, [
            ['slug' => 'graduation-thesis-fiqh', 'name_ar' => 'مشروع التخرج — الفقه والتفسير'],
        ]);

        // ── الحديث curriculum ──
        $this->assignSpecialized($hadithSpec, $year4, 1, [
            ['slug' => 'hadith-sahihayn', 'name_ar' => 'صحيحا البخاري ومسلم'],
            ['slug' => 'hadith-daeef', 'name_ar' => 'الحديث الضعيف'],
            $researchMethods,
        ]);
        $this->assignSpecialized($hadithSpec, $year4, 2, [
            ['slug' => 'hadith-rijaal', 'name_ar' => 'علم الرجال'],
            ['slug' => 'hadith-ilal', 'name_ar' => 'علل الحديث'],
        ]);
        $this->assignSpecialized($hadithSpec, $year5, 1, [
            ['slug' => 'hadith-takhrij', 'name_ar' => 'التخريج'],
        ]);
        $this->assignSpecialized($hadithSpec, $year5, 2, [
            ['slug' => 'graduation-thesis-hadith', 'name_ar' => 'مشروع التخرج — الحديث'],
        ]);

        // ── العقيدة curriculum ──
        $this->assignSpecialized($aqeedahSpec, $year4, 1, [
            ['slug' => 'aqeedah-tahawiyyah', 'name_ar' => 'العقيدة الطحاوية'],
            ['slug' => 'aqeedah-wasitiyyah', 'name_ar' => 'العقيدة الواسطية'],
            $researchMethods,
        ]);
        $this->assignSpecialized($aqeedahSpec, $year4, 2, [
            ['slug' => 'aqeedah-sects', 'name_ar' => 'الفرق والردود'],
        ]);
        $this->assignSpecialized($aqeedahSpec, $year5, 1, [
            ['slug' => 'aqeedah-contemporary', 'name_ar' => 'العقيدة وقضايا العصر'],
        ]);
        $this->assignSpecialized($aqeedahSpec, $year5, 2, [
            ['slug' => 'graduation-thesis-aqeedah', 'name_ar' => 'مشروع التخرج — العقيدة'],
        ]);

        $this->linkExistingCoursesToSubjects();
        $this->seedSpecializedStudentDemo($specialized, $year4, $fiqhSpec, $hadithSpec);
    }

    private function level(int $number, string $nameAr, string $slug, CurriculumType $type, string $description): AcademicLevel
    {
        return AcademicLevel::query()->updateOrCreate(
            ['number' => $number],
            [
                'name_ar' => $nameAr,
                'name_en' => $nameAr,
                'slug' => $slug,
                'curriculum_type' => $type,
                'description_ar' => $description,
                'sort_order' => $number,
                'is_active' => true,
            ]
        );
    }

    private function year(AcademicLevel $level, int $yearNumber, string $nameAr, string $slug): AcademicYear
    {
        return AcademicYear::query()->updateOrCreate(
            ['academic_level_id' => $level->id, 'slug' => $slug],
            [
                'name_ar' => $nameAr,
                'name_en' => $nameAr,
                'year_number' => $yearNumber,
                'academic_level_id' => $level->id,
                'sort_order' => $yearNumber,
                'is_active' => true,
            ]
        );
    }

    private function specialization(
        AcademicLevel $level,
        string $slug,
        string $nameAr,
        string $nameEn,
        int $sortOrder,
        bool $isActive,
    ): Specialization {
        return Specialization::query()->updateOrCreate(
            ['slug' => $slug],
            [
                'academic_level_id' => $level->id,
                'name_ar' => $nameAr,
                'name_en' => $nameEn,
                'description_ar' => "منهج تخصص {$nameAr} للسنة الرابعة والخامسة.",
                'sort_order' => $sortOrder,
                'is_active' => $isActive,
            ]
        );
    }

    private function subject(string $slug, string $nameAr, array $extra = []): Subject
    {
        return Subject::query()->updateOrCreate(
            ['slug' => $slug],
            array_merge(['name_ar' => $nameAr, 'is_active' => true], $extra)
        );
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
                    'is_required' => false,
                    'is_active' => true,
                ]
            );
        }
    }

    private function seedSpecializedStudentDemo(
        AcademicLevel $specializedLevel,
        AcademicYear $year4,
        Specialization $fiqhSpec,
        Specialization $hadithSpec,
    ): void {
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
                'academic_level_id' => $specializedLevel->id,
                'academic_year_id' => $year4->id,
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
