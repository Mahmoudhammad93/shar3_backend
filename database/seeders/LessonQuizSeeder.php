<?php

namespace Database\Seeders;

use App\Models\Course;
use App\Models\Lesson;
use App\Models\LessonQuestion;
use App\Models\LessonQuestionOption;
use Illuminate\Database\Seeder;

class LessonQuizSeeder extends Seeder
{
    /**
     * Each lesson includes one question per type: true_false → choice → text.
     *
     * @var array<string, array<int, list<array<string, mixed>>>>
     */
    private const QUIZZES = [
        'tafsir-al-baqarah' => [
            1 => [
                [
                    'question_ar' => 'سورة البقرة هي أطول سور القرآن الكريم.',
                    'type' => LessonQuestion::TYPE_TRUE_FALSE,
                    'correct_answer' => 'true',
                ],
                [
                    'question_ar' => 'ما هي أطول سور القرآن الكريم؟',
                    'type' => LessonQuestion::TYPE_CHOICE,
                    'options' => [
                        ['option_ar' => 'سورة البقرة', 'is_correct' => true],
                        ['option_ar' => 'سورة آل عمران', 'is_correct' => false],
                        ['option_ar' => 'سورة النساء', 'is_correct' => false],
                        ['option_ar' => 'سورة يوسف', 'is_correct' => false],
                    ],
                ],
                [
                    'question_ar' => 'اكتب اسم السورة التي يدرسها هذا المقرر.',
                    'type' => LessonQuestion::TYPE_TEXT,
                    'correct_answer' => 'سورة البقرة',
                ],
            ],
            2 => [
                [
                    'question_ar' => 'قوله تعالى «الكتاب لا ريب فيه» يثبت أن القرآن من عند الله.',
                    'type' => LessonQuestion::TYPE_TRUE_FALSE,
                    'correct_answer' => 'true',
                ],
                [
                    'question_ar' => 'من هم الذين ينتفعون بالقرآن كما في «هدى للمتقين»؟',
                    'type' => LessonQuestion::TYPE_CHOICE,
                    'options' => [
                        ['option_ar' => 'المتقون', 'is_correct' => true],
                        ['option_ar' => 'العلماء فقط', 'is_correct' => false],
                        ['option_ar' => 'العرب فقط', 'is_correct' => false],
                        ['option_ar' => 'من حفظ القرآن دون فهم', 'is_correct' => false],
                    ],
                ],
                [
                    'question_ar' => 'اكتب أحد الحروف المقطعة التي افتتحت بها سورة البقرة.',
                    'type' => LessonQuestion::TYPE_TEXT,
                    'correct_answer' => 'الم',
                ],
            ],
            3 => [
                [
                    'question_ar' => 'يُستحب قراءة آية الكرسي بعد كل صلاة.',
                    'type' => LessonQuestion::TYPE_TRUE_FALSE,
                    'correct_answer' => 'true',
                ],
                [
                    'question_ar' => 'ما معنى «الحيّ القيّوم» في آية الكرسي؟',
                    'type' => LessonQuestion::TYPE_CHOICE,
                    'options' => [
                        ['option_ar' => 'الحي الذي لا يموت، القائم على كل شيء', 'is_correct' => true],
                        ['option_ar' => 'اسم ملك من الملائكة', 'is_correct' => false],
                        ['option_ar' => 'صفة للنبي ﷺ', 'is_correct' => false],
                        ['option_ar' => 'معنى زماني مؤقت', 'is_correct' => false],
                    ],
                ],
                [
                    'question_ar' => 'اكتب اسم الآية الكريمة التي يتناولها هذا الدرس.',
                    'type' => LessonQuestion::TYPE_TEXT,
                    'correct_answer' => 'آية الكرسي',
                ],
            ],
            4 => [
                [
                    'question_ar' => 'حكمة الصيام تحقيق التقوى كما في قوله «لعلكم تتقون».',
                    'type' => LessonQuestion::TYPE_TRUE_FALSE,
                    'correct_answer' => 'true',
                ],
                [
                    'question_ar' => 'من يُرخص لهم الفطر في الصيام حسب الدرس؟',
                    'type' => LessonQuestion::TYPE_CHOICE,
                    'options' => [
                        ['option_ar' => 'المسافر والمريض', 'is_correct' => true],
                        ['option_ar' => 'الغني والفقير', 'is_correct' => false],
                        ['option_ar' => 'الشاب والكبير', 'is_correct' => false],
                        ['option_ar' => 'من لم يحفظ القرآن', 'is_correct' => false],
                    ],
                ],
                [
                    'question_ar' => 'اكتب العبادة التي تناولها الدرس من أحكام سورة البقرة.',
                    'type' => LessonQuestion::TYPE_TEXT,
                    'correct_answer' => 'الصيام',
                ],
            ],
        ],
        'fiqh-ibadat' => [
            1 => [
                [
                    'question_ar' => 'مراتب الأحكام الشرعية خمس: الواجب والمندوب والمحرّم والمكروه والمباح.',
                    'type' => LessonQuestion::TYPE_TRUE_FALSE,
                    'correct_answer' => 'true',
                ],
                [
                    'question_ar' => 'ما المقصود بالعبادات في الدرس؟',
                    'type' => LessonQuestion::TYPE_CHOICE,
                    'options' => [
                        ['option_ar' => 'ما يُتقرب به إلى الله من أقوال وأفعال', 'is_correct' => true],
                        ['option_ar' => 'الصلاة فقط', 'is_correct' => false],
                        ['option_ar' => 'الأعمال الدنيوية', 'is_correct' => false],
                        ['option_ar' => 'العبادات البدعية', 'is_correct' => false],
                    ],
                ],
                [
                    'question_ar' => 'اكتب فرع الفقه الذي يدرسه هذا المقرر.',
                    'type' => LessonQuestion::TYPE_TEXT,
                    'correct_answer' => 'فقه العبادات',
                ],
            ],
            2 => [
                [
                    'question_ar' => 'يجب الغسل على المسلم عند الجنابة.',
                    'type' => LessonQuestion::TYPE_TRUE_FALSE,
                    'correct_answer' => 'true',
                ],
                [
                    'question_ar' => 'ما من أركان الوضوء الصحيح؟',
                    'type' => LessonQuestion::TYPE_CHOICE,
                    'options' => [
                        ['option_ar' => 'غسل الوجه ومسح الرأس وغسل اليدين والرجلين', 'is_correct' => true],
                        ['option_ar' => 'الاغتسال الكامل', 'is_correct' => false],
                        ['option_ar' => 'التيمم فقط', 'is_correct' => false],
                        ['option_ar' => 'غسل اليدين دون غيرهما', 'is_correct' => false],
                    ],
                ],
                [
                    'question_ar' => 'اكتب نوع الطهارة الواجب قبل الصلاة.',
                    'type' => LessonQuestion::TYPE_TEXT,
                    'correct_answer' => 'الوضوء',
                ],
            ],
            3 => [
                [
                    'question_ar' => 'الكلام العمد والضحك من مبطلات الصلاة.',
                    'type' => LessonQuestion::TYPE_TRUE_FALSE,
                    'correct_answer' => 'true',
                ],
                [
                    'question_ar' => 'ما من شروط صحة الصلاة؟',
                    'type' => LessonQuestion::TYPE_CHOICE,
                    'options' => [
                        ['option_ar' => 'الطهارة واستقبال القبلة ودخول الوقت', 'is_correct' => true],
                        ['option_ar' => 'الصوم نهاراً', 'is_correct' => false],
                        ['option_ar' => 'الحج فقط', 'is_correct' => false],
                        ['option_ar' => 'قراءة القرآن كاملاً', 'is_correct' => false],
                    ],
                ],
                [
                    'question_ar' => 'اكتب أعظم العبادات بعد الشهادتين في الإسلام.',
                    'type' => LessonQuestion::TYPE_TEXT,
                    'correct_answer' => 'الصلاة',
                ],
            ],
        ],
        'ulum-al-hadith' => [
            1 => [
                [
                    'question_ar' => 'الحديث الشريف هو ما أُضيف إلى النبي ﷺ من قول أو فعل أو تقرير.',
                    'type' => LessonQuestion::TYPE_TRUE_FALSE,
                    'correct_answer' => 'true',
                ],
                [
                    'question_ar' => 'لماذا نشأت علوم الحديث؟',
                    'type' => LessonQuestion::TYPE_CHOICE,
                    'options' => [
                        ['option_ar' => 'لحفظ السنة وتمييز الصحيح من الضعيف', 'is_correct' => true],
                        ['option_ar' => 'لتأليف كتب الشعر', 'is_correct' => false],
                        ['option_ar' => 'لترجمة القرآن', 'is_correct' => false],
                        ['option_ar' => 'لجمع الأحاديث دون تمحيص', 'is_correct' => false],
                    ],
                ],
                [
                    'question_ar' => 'اكتب اسم العلم الذي يدرسه هذا المقرر.',
                    'type' => LessonQuestion::TYPE_TEXT,
                    'correct_answer' => 'علوم الحديث',
                ],
            ],
            2 => [
                [
                    'question_ar' => 'من شروط قبول الراوي العدالة والضبط.',
                    'type' => LessonQuestion::TYPE_TRUE_FALSE,
                    'correct_answer' => 'true',
                ],
                [
                    'question_ar' => 'ما علم الجرح والتعديل؟',
                    'type' => LessonQuestion::TYPE_CHOICE,
                    'options' => [
                        ['option_ar' => 'علم يُعرف به حال الرواة من عدالة وضبط', 'is_correct' => true],
                        ['option_ar' => 'علم النحو', 'is_correct' => false],
                        ['option_ar' => 'علم الفقه المقارن', 'is_correct' => false],
                        ['option_ar' => 'علم التفسير بالرأي', 'is_correct' => false],
                    ],
                ],
                [
                    'question_ar' => 'اكتب اسم من ينقل الحديث عن النبي ﷺ.',
                    'type' => LessonQuestion::TYPE_TEXT,
                    'correct_answer' => 'الراوي',
                ],
            ],
        ],
        'islamic-aqeedah' => [
            1 => [
                [
                    'question_ar' => 'مصدر العقيدة الصحيحة هو القرآن والسنة على فهم السلف.',
                    'type' => LessonQuestion::TYPE_TRUE_FALSE,
                    'correct_answer' => 'true',
                ],
                [
                    'question_ar' => 'لماذا يجب التمسك بعقيدة السلف الصالح؟',
                    'type' => LessonQuestion::TYPE_CHOICE,
                    'options' => [
                        ['option_ar' => 'لأنهم أفهم الناس للكتاب والسنة', 'is_correct' => true],
                        ['option_ar' => 'لأنهم أقرب إلى زماننا', 'is_correct' => false],
                        ['option_ar' => 'لأنهم تركوا السنة', 'is_correct' => false],
                        ['option_ar' => 'لأنهم اختلفوا في أصول الإيمان', 'is_correct' => false],
                    ],
                ],
                [
                    'question_ar' => 'اكتب العلم الذي يتناوله هذا المقرر.',
                    'type' => LessonQuestion::TYPE_TEXT,
                    'correct_answer' => 'العقيدة',
                ],
            ],
            2 => [
                [
                    'question_ar' => 'أول ركن من أركان الإيمان هو الإيمان بالله.',
                    'type' => LessonQuestion::TYPE_TRUE_FALSE,
                    'correct_answer' => 'true',
                ],
                [
                    'question_ar' => 'ما موقف أهل السنة من صفات الله؟',
                    'type' => LessonQuestion::TYPE_CHOICE,
                    'options' => [
                        ['option_ar' => 'إثباتها على الوجه اللائق بجلال الله دون تحريف', 'is_correct' => true],
                        ['option_ar' => 'تأويلها كلها على غير ظاهرها', 'is_correct' => false],
                        ['option_ar' => 'نفيها كلها', 'is_correct' => false],
                        ['option_ar' => 'تشبيهها بالمخلوق', 'is_correct' => false],
                    ],
                ],
                [
                    'question_ar' => 'اكتب عدد أركان الإيمان.',
                    'type' => LessonQuestion::TYPE_TEXT,
                    'correct_answer' => 'ستة',
                ],
            ],
        ],
    ];

    public function run(): void
    {
        foreach (self::QUIZZES as $courseSlug => $lessons) {
            $course = Course::query()->where('slug', $courseSlug)->first();
            if (! $course) {
                continue;
            }

            foreach ($lessons as $sortOrder => $questions) {
                $lesson = Lesson::query()
                    ->where('course_id', $course->id)
                    ->where('sort_order', $sortOrder)
                    ->first();

                if (! $lesson) {
                    continue;
                }

                $this->seedLessonQuiz($lesson, $questions);
            }
        }
    }

    /**
     * @param  list<array<string, mixed>>  $questions
     */
    public function seedLessonQuiz(Lesson $lesson, array $questions): void
    {
        $lesson->questions()->each(function (LessonQuestion $question): void {
            $question->options()->delete();
            $question->delete();
        });

        foreach ($questions as $questionIndex => $questionData) {
            $type = $questionData['type'] ?? LessonQuestion::TYPE_CHOICE;

            $question = LessonQuestion::query()->create([
                'lesson_id' => $lesson->id,
                'question_ar' => $questionData['question_ar'],
                'type' => $type,
                'correct_answer' => $questionData['correct_answer'] ?? null,
                'sort_order' => $questionIndex + 1,
            ]);

            if ($type !== LessonQuestion::TYPE_CHOICE || empty($questionData['options'])) {
                continue;
            }

            foreach ($questionData['options'] as $optionIndex => $optionData) {
                LessonQuestionOption::query()->create([
                    'lesson_question_id' => $question->id,
                    'option_ar' => $optionData['option_ar'],
                    'is_correct' => $optionData['is_correct'],
                    'sort_order' => $optionIndex + 1,
                ]);
            }
        }
    }
}
