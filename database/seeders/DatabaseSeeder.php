<?php

namespace Database\Seeders;

use App\Models\Announcement;
use App\Models\Assignment;
use App\Models\Category;
use App\Models\Certificate;
use App\Models\Course;
use App\Models\Enrollment;
use App\Models\Faq;
use App\Models\HeroSlide;
use App\Models\Lesson;
use App\Models\Program;
use App\Models\Schedule;
use App\Models\Student;
use App\Models\Teacher;
use App\Models\Testimonial;
use App\Models\User;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call(SiteSettingSeeder::class);

        User::query()->firstOrCreate(
            ['email' => 'admin@share3a.com'],
            ['name' => 'مدير النظام', 'password' => 'password', 'role' => 'admin']
        );

        HeroSlide::query()->create([
            'title_ar' => 'معهد علم شرعي',
            'subtitle_ar' => 'طلب العلم فريضة على كل مسلم',
            'button_text_ar' => 'سجّل الآن',
            'button_url' => '/courses',
            'sort_order' => 1,
            'is_active' => true,
        ]);

        HeroSlide::query()->create([
            'title_ar' => 'برامج علمية متكاملة',
            'subtitle_ar' => 'من المبتدئ إلى المتقدم في العلوم الشرعية',
            'button_text_ar' => 'استكشف البرامج',
            'button_url' => '/programs',
            'sort_order' => 2,
            'is_active' => true,
        ]);

        $categories = collect([
            ['name_ar' => 'القرآن الكريم', 'slug' => 'quran'],
            ['name_ar' => 'الفقه', 'slug' => 'fiqh'],
            ['name_ar' => 'الحديث', 'slug' => 'hadith'],
            ['name_ar' => 'العقيدة', 'slug' => 'aqeedah'],
            ['name_ar' => 'اللغة العربية', 'slug' => 'arabic'],
        ])->map(fn ($data, $i) => Category::query()->create([...$data, 'sort_order' => $i + 1]));

        $programs = collect([
            ['name_ar' => 'السنة التمهيدية', 'slug' => 'preparatory-program', 'duration' => 'سنة واحدة', 'level' => 'تأسيس', 'description_ar' => 'سنة عامة لجميع الطلاب — إطلاع على مبادئ العلوم الشرعية واللغوية.'],
            ['name_ar' => 'مستوى التأصيل العلمي', 'slug' => 'taaseel-program', 'duration' => 'سنتان', 'level' => 'تأصيل', 'description_ar' => 'السنة الثانية والثالثة — توسيع وتأصيل في العلوم الشرعية.'],
            ['name_ar' => 'مستوى التخصص', 'slug' => 'specialization-program', 'duration' => 'سنتان', 'level' => 'تخصص', 'description_ar' => 'السنة الرابعة والخامسة — التخصص في إحدى الشعب الأربع.'],
        ])->map(fn ($data, $i) => Program::query()->create([...$data, 'sort_order' => $i + 1]));

        $teachers = collect([
            ['name_ar' => 'د. عبدالله بن محمد', 'slug' => 'abdullah', 'title_ar' => 'أستاذ الفقه', 'specializations' => 'الفقه، أصول الفقه'],
            ['name_ar' => 'د. سعد بن أحمد', 'slug' => 'saad', 'title_ar' => 'أستاذ الحديث', 'specializations' => 'الحديث، مصطلح الحديث'],
            ['name_ar' => 'د. فيصل بن خالد', 'slug' => 'faisal', 'title_ar' => 'أستاذ العقيدة', 'specializations' => 'العقيدة، الفرق'],
        ])->map(fn ($data, $i) => Teacher::query()->create([...$data, 'is_featured' => true, 'sort_order' => $i + 1]));

        $courses = [
            [
                'title_ar' => 'تفسير سورة البقرة',
                'slug' => 'tafsir-al-baqarah',
                'image' => 'https://placehold.co/800x500/0a3d34/c9a227/png?text=Tafsir',
                'category_id' => $categories[0]->id,
                'program_id' => $programs[1]->id,
                'teacher_id' => $teachers[0]->id,
                'description_ar' => 'دراسة تفسيرية لسورة البقرة مع التركيز على أهم المسائل الفقهية والعقدية.',
                'duration_hours' => 40,
                'level' => 'متوسط',
                'is_featured' => true,
                'is_published' => true,
            ],
            [
                'title_ar' => 'فقه العبادات',
                'slug' => 'fiqh-ibadat',
                'image' => 'https://placehold.co/800x500/004d40/ffffff/png?text=Fiqh',
                'category_id' => $categories[1]->id,
                'program_id' => $programs[0]->id,
                'teacher_id' => $teachers[0]->id,
                'description_ar' => 'شرح مبسط لأحكام الطهارة والصلاة والصيام والزكاة والحج.',
                'duration_hours' => 30,
                'level' => 'مبتدئ',
                'is_featured' => true,
                'is_published' => true,
            ],
            [
                'title_ar' => 'علوم الحديث',
                'slug' => 'ulum-al-hadith',
                'image' => 'https://placehold.co/800x500/78350f/ffffff/png?text=Hadith',
                'category_id' => $categories[2]->id,
                'program_id' => $programs[2]->id,
                'teacher_id' => $teachers[1]->id,
                'description_ar' => 'مقدمة في علوم الحديث: الراوي، المحدث، الصحيح، الحسن، الضعيف.',
                'duration_hours' => 35,
                'level' => 'متقدم',
                'is_featured' => true,
                'is_published' => true,
            ],
            [
                'title_ar' => 'العقيدة الإسلامية',
                'slug' => 'islamic-aqeedah',
                'image' => 'https://placehold.co/800x500/1e293b/c9a227/png?text=Aqeedah',
                'category_id' => $categories[3]->id,
                'program_id' => $programs[0]->id,
                'teacher_id' => $teachers[2]->id,
                'description_ar' => 'دراسة أصول الإيمان الستة على منهج Salaf.',
                'duration_hours' => 25,
                'level' => 'مبتدئ',
                'is_published' => true,
            ],
        ];

        foreach ($courses as $i => $courseData) {
            $course = Course::query()->create([...$courseData, 'sort_order' => $i + 1, 'is_free' => true]);

            Lesson::query()->create([
                'course_id' => $course->id,
                'title_ar' => 'المقدمة',
                'content_ar' => 'مقدمة في '.$course->title_ar,
                'sort_order' => 1,
                'is_published' => true,
            ]);
        }

        Announcement::query()->create([
            'title_ar' => 'بدء التسجيل للفصل الجديد',
            'slug' => 'new-semester-registration',
            'excerpt_ar' => 'يسر معهد علم شرعي أن يعلن عن فتح باب التسجيل للفصل الدراسي الجديد.',
            'content_ar' => 'يسر معهد علم شرعي أن يعلن عن فتح باب التسجيل للفصل الدراسي الجديد. للتسجيل يرجى زيارة صفحة الدورات.',
            'is_published' => true,
            'published_at' => now(),
        ]);

        Testimonial::query()->create([
            'name_ar' => 'محمد الأحمد',
            'role_ar' => 'طالب في البرنامج المتوسط',
            'content_ar' => 'تجربة رائعة في طلب العلم، المنهج منظم والمعلمون على مستوى عالٍ من العلم والأخلاق.',
            'rating' => 5,
            'is_featured' => true,
        ]);

        Testimonial::query()->create([
            'name_ar' => 'فاطمة السالم',
            'role_ar' => 'طالبة في البرنامج التمهيدي',
            'content_ar' => 'المعهد وفّر لي بيئة مناسبة لتعلم العلوم الشرعية رغم انشغالي بالعمل.',
            'rating' => 5,
            'is_featured' => true,
        ]);

        Faq::query()->create([
            'question_ar' => 'هل الدورات مجانية؟',
            'answer_ar' => 'نعم، جميع الدورات الأساسية مجانية. بعض البرامج المتقدمة قد تتطلب رسوماً رمزية.',
            'sort_order' => 1,
        ]);

        Faq::query()->create([
            'question_ar' => 'هل التعليم عن بُعد؟',
            'answer_ar' => 'نعم، جميع الدورات متاحة عبر الإنترنت مع إمكانية الحضور المباشر.',
            'sort_order' => 2,
        ]);

        // Demo student account
        $demoUser = User::query()->firstOrCreate(
            ['email' => 'student@share3a.com'],
            ['name' => 'أحمد الطالب', 'password' => 'password', 'role' => 'student']
        );

        $demoStudent = Student::query()->firstOrCreate(
            ['email' => 'student@share3a.com'],
            ['user_id' => $demoUser->id, 'name' => 'أحمد الطالب', 'phone' => '+966511111111', 'status' => Student::STATUS_ACTIVE]
        );

        $firstCourse = Course::query()->first();
        if ($firstCourse) {
            Enrollment::query()->firstOrCreate(
                ['student_id' => $demoStudent->id, 'course_id' => $firstCourse->id],
                ['status' => 'approved', 'enrolled_at' => now()]
            );

            Assignment::query()->firstOrCreate(
                ['course_id' => $firstCourse->id, 'title_ar' => 'واجب أول: ملخص الدرس'],
                ['description_ar' => 'اكتب ملخصاً للدرس الأول', 'due_at' => now()->addDays(7), 'is_published' => true]
            );

            Schedule::query()->firstOrCreate(
                ['course_id' => $firstCourse->id, 'title_ar' => 'محاضرة مباشرة'],
                ['description_ar' => 'محاضرة تفاعلية مع المعلم', 'type' => 'live', 'starts_at' => now()->addDays(2), 'is_published' => true]
            );

            Certificate::query()->firstOrCreate(
                ['student_id' => $demoStudent->id, 'course_id' => $firstCourse->id],
                ['certificate_number' => 'SHR-2026-001', 'issued_at' => now()]
            );
        }

        $this->call(AcademicStructureSeeder::class);
        $this->call(SubjectCourseSeeder::class);
        $this->call(StudyPlanContentSeeder::class);
    }
}
