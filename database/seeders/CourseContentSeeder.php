<?php

namespace Database\Seeders;

use App\Models\Course;
use App\Models\Lesson;
use Illuminate\Database\Seeder;

class CourseContentSeeder extends Seeder
{
    /** Verified embeddable YouTube URLs (oembed-checked). */
    private const VIDEOS = [
        'tafsir_intro' => 'https://www.youtube.com/watch?v=3a7a55bEFho',
        'baqarah_keys' => 'https://www.youtube.com/watch?v=0Jtvzk3L9v0',
        'ayat_kursi' => 'https://www.youtube.com/watch?v=13FX677OZb4',
        'fasting_fiqh' => 'https://www.youtube.com/watch?v=4-EBtl5tjnI',
        'fiqh_intro' => 'https://www.youtube.com/watch?v=4qzun6leO_c',
        'wudu' => 'https://www.youtube.com/watch?v=4u6loNndUbA',
        'salah' => 'https://www.youtube.com/watch?v=2r5l86l-xaU',
        'hadith_intro' => 'https://www.youtube.com/watch?v=0NvCfoRU7wc',
        'hadith_authenticity' => 'https://www.youtube.com/watch?v=1RJnNzJU7Vg',
        'aqeedah_intro' => 'https://www.youtube.com/watch?v=08tfZ8xxtD4',
        'aqeedah_allah' => 'https://www.youtube.com/watch?v=0qnvB4XOw_g',
    ];

    public function run(): void
    {
        $courses = [
            'tafsir-al-baqarah' => [
                [
                    'title_ar' => 'المقدمة',
                    'content_ar' => <<<'TEXT'
مرحباً بكم في دورة تفسير سورة البقرة.

في هذا الدرس نتعرّف على:
• فضل سورة البقرة ومنزلتها في القرآن الكريم
• أسباب نزول السورة ومكيّها ومدنّيّها
• منهجنا في التفسير: بالقرآن، وبالسنة، وأقوال السلف

سورة البقرة أطول سور القرآن، وتتضمن أحكاماً عقدية وفقهية وإيمانية كثيرة، وهي حرز للمسلم من الشيطان كما جاء في الحديث الصحيح.
TEXT,
                    'video_url' => self::VIDEOS['tafsir_intro'],
                    'duration_minutes' => 25,
                    'sort_order' => 1,
                ],
                [
                    'title_ar' => 'مفاتيح سورة البقرة',
                    'content_ar' => <<<'TEXT'
يتناول هذا الدرس مفاتيح فهم سورة البقرة:

1. **الم**: من الحروف المقطّعة التي افتتح الله بها السورة
2. **الكتاب لا ريب فيه**: إثبات أن القرآن من عند الله
3. **هدى للمتقين**: بيان صفات المتقين الذين ينتفعون بالقرآن

نشرح معاني هذه الآيات ونربطها بموضوعات السورة الكبرى: الإيمان، القصص، الأحكام، والموعظة.
TEXT,
                    'video_url' => self::VIDEOS['baqarah_keys'],
                    'duration_minutes' => 30,
                    'sort_order' => 2,
                ],
                [
                    'title_ar' => 'تفسير آية الكرسي (1)',
                    'content_ar' => <<<'TEXT'
**آية الكرسي** — قال تعالى: ﴿اللَّهُ لَا إِلَٰهَ إِلَّا هُوَ الْحَيُّ الْقَيُّومُ﴾

في هذا الدرس:
• معنى **الحيّ** و**القيّوم** و**الكرسي**
• إثبات الصفات لله تعالى على الوجه اللائق بجلاله
• فضل تلاوة آية الكرسي بعد كل صلاة

هذا الدرس الأول من دروس مخصّصة لآية الكرسي، وسنتابع في الدرس التالي بقية الآية.
TEXT,
                    'video_url' => self::VIDEOS['ayat_kursi'],
                    'duration_minutes' => 35,
                    'sort_order' => 3,
                ],
                [
                    'title_ar' => 'أحكام الصيام في سورة البقرة',
                    'content_ar' => <<<'TEXT'
قال تعالى: ﴿يَا أَيُّهَا الَّذِينَ آمَنُوا كُتِبَ عَلَيْكُمُ الصِّيَامُ كَمَا كُتِبَ عَلَى الَّذِينَ مِن قَبْلِكُمْ لَعَلَّكُمْ تَتَّقُونَ﴾

نناقش:
• حكمة مشروعية الصيام
• شروط وجوب الصيام
• من أُريد منهم الفطر: المسافر والمريض
• أحكام القضاء والكفارة

مع التطبيقات المعاصرة والأسئلة الشائعة.
TEXT,
                    'video_url' => self::VIDEOS['fasting_fiqh'],
                    'duration_minutes' => 40,
                    'sort_order' => 4,
                ],
            ],
            'fiqh-ibadat' => [
                [
                    'title_ar' => 'المقدمة في فقه العبادات',
                    'content_ar' => 'تعريف بالعبادات وأقسامها، ومراتب الأحكام الشرعية: الواجب، المندوب، المحرّم، المكروه، والمباح.',
                    'video_url' => self::VIDEOS['fiqh_intro'],
                    'duration_minutes' => 20,
                    'sort_order' => 1,
                ],
                [
                    'title_ar' => 'أحكام الطهارة',
                    'content_ar' => 'الوضوء والغسل والتيمم: شروطها، أركانها، نواقضها، مع أمثلة تطبيقية من الحياة اليومية.',
                    'video_url' => self::VIDEOS['wudu'],
                    'duration_minutes' => 35,
                    'sort_order' => 2,
                ],
                [
                    'title_ar' => 'أحكام الصلاة',
                    'content_ar' => 'شروط الصلاة، أركانها، واجباتها، سننها، ومبطلاتها. مع بيان صلاة الجماعة والمسافر.',
                    'video_url' => self::VIDEOS['salah'],
                    'duration_minutes' => 45,
                    'sort_order' => 3,
                ],
            ],
            'ulum-al-hadith' => [
                [
                    'title_ar' => 'تعريف علوم الحديث',
                    'content_ar' => 'تعريف الحديث الشريف، وأهمية علوم الحديث في حفظ السنة، ونشأة تدوين الحديث.',
                    'video_url' => self::VIDEOS['hadith_intro'],
                    'duration_minutes' => 25,
                    'sort_order' => 1,
                ],
                [
                    'title_ar' => 'الراوي والمحدّث',
                    'content_ar' => 'شروط قبول الراوي، درجات الرواة، وعلوم الجرح والتعديل.',
                    'video_url' => self::VIDEOS['hadith_authenticity'],
                    'duration_minutes' => 30,
                    'sort_order' => 2,
                ],
            ],
            'islamic-aqeedah' => [
                [
                    'title_ar' => 'مقدمة في العقيدة',
                    'content_ar' => 'تعريف العقيدة الإسلامية، مصادرها، وأهمية التمسك بعقيدة السلف الصالح.',
                    'video_url' => self::VIDEOS['aqeedah_intro'],
                    'duration_minutes' => 20,
                    'sort_order' => 1,
                ],
                [
                    'title_ar' => 'الإيمان بالله',
                    'content_ar' => 'أدلة وجود الله، أسماء الله وصفاته، وموقف أهل السنة من الصفات.',
                    'video_url' => self::VIDEOS['aqeedah_allah'],
                    'duration_minutes' => 35,
                    'sort_order' => 2,
                ],
            ],
        ];

        foreach ($courses as $slug => $lessons) {
            $course = Course::query()->where('slug', $slug)->first();
            if (! $course) {
                continue;
            }

            Lesson::query()->where('course_id', $course->id)->delete();

            foreach ($lessons as $lesson) {
                Lesson::query()->create([
                    'course_id' => $course->id,
                    'title_ar' => $lesson['title_ar'],
                    'content_ar' => $lesson['content_ar'],
                    'video_url' => $lesson['video_url'] ?? null,
                    'duration_minutes' => $lesson['duration_minutes'] ?? null,
                    'sort_order' => $lesson['sort_order'],
                    'is_published' => true,
                ]);
            }
        }

        $this->call(LessonQuizSeeder::class);
    }
}
