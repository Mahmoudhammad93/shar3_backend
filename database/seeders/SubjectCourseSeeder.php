<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Course;
use App\Models\Lesson;
use App\Models\Program;
use App\Models\Subject;
use App\Models\Teacher;
use Illuminate\Database\Seeder;

class SubjectCourseSeeder extends Seeder
{
    /** @var array<string, string> */
    private array $existingCourseLinks = [
        'y1s1-tawheed' => 'islamic-aqeedah',
        'y1s1-fiqh' => 'fiqh-ibadat',
        'y1s1-mustalah' => 'ulum-al-hadith',
        'y1s1-tafsir' => 'tafsir-al-baqarah',
    ];

    public function run(): void
    {
        foreach ($this->existingCourseLinks as $subjectSlug => $courseSlug) {
            $subject = Subject::query()->where('slug', $subjectSlug)->first();

            if (! $subject) {
                continue;
            }

            $course = Course::query()->where('slug', $courseSlug)->first()
                ?? Course::query()->firstOrCreate(
                    ['slug' => $courseSlug],
                    [
                        'title_ar' => $subject->name_ar,
                        'description_ar' => $subject->primary_text_ar ?? $subject->name_ar,
                        'level' => 'مبتدئ',
                        'is_published' => true,
                    ]
                );

            if ($course->lessons()->count() === 0) {
                Lesson::query()->create([
                    'course_id' => $course->id,
                    'title_ar' => 'مقدمة في '.$subject->name_ar,
                    'content_ar' => $subject->primary_text_ar ?? 'مقدمة في '.$subject->name_ar,
                    'duration_minutes' => 30,
                    'sort_order' => 1,
                    'is_published' => true,
                ]);
            }

            $subject->update(['course_id' => $course->id]);
        }

        $category = Category::query()->first();
        $program = Program::query()->where('slug', 'preparatory-program')->first()
            ?? Program::query()->first();
        $teacher = Teacher::query()->first();

        $generated = [
            'y1s1-seerah' => [
                'title' => 'السيرة النبوية — الفصل الأول',
                'lessons' => [
                    ['title_ar' => 'مقدمة في السيرة', 'content_ar' => 'التعريف بالسيرة النبوية وأهميتها في فهم الدين.'],
                    ['title_ar' => 'مولد النبي ﷺ ونشأته', 'content_ar' => 'دراسة مرحلة مكة قبل البعثة وصفات النبي ﷺ.'],
                ],
            ],
            'y1s1-nahw' => [
                'title' => 'النحو — الآجرومية',
                'lessons' => [
                    ['title_ar' => 'المقدمة في النحو', 'content_ar' => 'تعريف النحو وعلاقته بفهم القرآن والحديث.'],
                    ['title_ar' => 'أقسام الكلام', 'content_ar' => 'الاسم والفعل والحرف مع أمثلة تطبيقية.'],
                ],
            ],
            'y1s1-usul-fiqh' => [
                'title' => 'أصول الفقه — رسالة لطيفة',
                'lessons' => [
                    ['title_ar' => 'مدخل إلى أصول الفقه', 'content_ar' => 'تعريف بعلم أصول الفقه ومراتبه.'],
                    ['title_ar' => 'الأدلة الشرعية', 'content_ar' => 'الكتاب والسنة والإجماع والقياس.'],
                ],
            ],
            'y1s1-memorization' => [
                'title' => 'مقرر الحفظ — جزء عم',
                'lessons' => [
                    ['title_ar' => 'خطة الحفظ', 'content_ar' => 'آداب الحفظ وخطة حفظ جزء عم للمبتدئين.'],
                    ['title_ar' => 'تطبيق الحفظ', 'content_ar' => 'مراجعة السور المقررة وضوابط الإتقان.'],
                ],
            ],
        ];

        foreach ($generated as $subjectSlug => $config) {
            $subject = Subject::query()->where('slug', $subjectSlug)->first();

            if (! $subject) {
                continue;
            }

            $course = Course::query()->firstOrCreate(
                ['slug' => 'subject-'.$subjectSlug],
                [
                    'title_ar' => $config['title'],
                    'category_id' => $category?->id,
                    'program_id' => $program?->id,
                    'teacher_id' => $teacher?->id,
                    'description_ar' => $subject->primary_text_ar ?? $subject->name_ar,
                    'duration_hours' => count($config['lessons']) * 2,
                    'level' => 'مبتدئ',
                    'is_published' => true,
                ]
            );

            if ($course->lessons()->count() === 0) {
                foreach ($config['lessons'] as $index => $lessonData) {
                    Lesson::query()->create([
                        'course_id' => $course->id,
                        'title_ar' => $lessonData['title_ar'],
                        'content_ar' => $lessonData['content_ar'],
                        'duration_minutes' => 30,
                        'sort_order' => $index + 1,
                        'is_published' => true,
                    ]);
                }
            }

            if ($subject->course_id !== $course->id) {
                $subject->update(['course_id' => $course->id]);
            }
        }
    }
}
