<?php

namespace Tests\Feature;

use App\Models\Course;
use App\Models\Enrollment;
use App\Models\Lesson;
use App\Models\LessonProgress;
use App\Models\LessonQuestion;
use App\Models\LessonQuestionOption;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class QuizTest extends TestCase
{
    use RefreshDatabase;

    private function enrolledLessonWithQuiz(): array
    {
        $student = $this->actingAsStudent();
        $course = Course::query()->create([
            'title_ar' => 'دورة',
            'slug' => 'course-quiz',
            'is_published' => true,
        ]);

        Enrollment::query()->create([
            'student_id' => $student->id,
            'course_id' => $course->id,
            'status' => Enrollment::STATUS_APPROVED,
            'enrolled_at' => now(),
        ]);

        $lesson = Lesson::query()->create([
            'course_id' => $course->id,
            'title_ar' => 'درس',
            'sort_order' => 1,
            'is_published' => true,
        ]);

        $question = LessonQuestion::query()->create([
            'lesson_id' => $lesson->id,
            'question_ar' => 'سؤال',
            'type' => LessonQuestion::TYPE_CHOICE,
            'sort_order' => 1,
        ]);

        $correct = LessonQuestionOption::query()->create([
            'lesson_question_id' => $question->id,
            'option_ar' => 'صح',
            'is_correct' => true,
            'sort_order' => 1,
        ]);

        LessonQuestionOption::query()->create([
            'lesson_question_id' => $question->id,
            'option_ar' => 'خطأ',
            'is_correct' => false,
            'sort_order' => 2,
        ]);

        return [$student, $lesson, $question, $correct];
    }

    public function test_quiz_does_not_expose_correct_answers(): void
    {
        [, $lesson] = $this->enrolledLessonWithQuiz();

        $response = $this->getJson("/api/v1/student/lessons/{$lesson->id}/quiz");

        $response->assertOk();
        $payload = json_encode($response->json(), JSON_UNESCAPED_UNICODE);

        $this->assertStringNotContainsString('is_correct', $payload);
        $this->assertStringNotContainsString('correct_answer', $payload);
    }

    public function test_student_can_submit_correct_quiz_answer(): void
    {
        [, $lesson, $question, $correct] = $this->enrolledLessonWithQuiz();

        $this->postJson("/api/v1/student/lessons/{$lesson->id}/quiz", [
            'answers' => [$question->id => $correct->id],
        ])->assertOk()->assertJsonPath('passed', true);

        $this->assertTrue(
            LessonProgress::query()
                ->where('lesson_id', $lesson->id)
                ->value('quiz_passed')
        );
    }

    public function test_incorrect_quiz_answer_is_rejected(): void
    {
        [, $lesson, $question] = $this->enrolledLessonWithQuiz();

        $wrongOption = LessonQuestionOption::query()
            ->where('lesson_question_id', $question->id)
            ->where('is_correct', false)
            ->first();

        $this->postJson("/api/v1/student/lessons/{$lesson->id}/quiz", [
            'answers' => [$question->id => $wrongOption->id],
        ])->assertStatus(422)->assertJsonPath('passed', false);
    }

    public function test_invalid_option_id_is_rejected(): void
    {
        [, $lesson, $question] = $this->enrolledLessonWithQuiz();

        $this->postJson("/api/v1/student/lessons/{$lesson->id}/quiz", [
            'answers' => [$question->id => 999999],
        ])->assertStatus(422);
    }

    public function test_unenrolled_student_cannot_access_quiz(): void
    {
        $this->actingAsStudent();

        $course = Course::query()->create([
            'title_ar' => 'دورة',
            'slug' => 'locked-course',
            'is_published' => true,
        ]);

        $lesson = Lesson::query()->create([
            'course_id' => $course->id,
            'title_ar' => 'درس',
            'sort_order' => 1,
            'is_published' => true,
        ]);

        $this->getJson("/api/v1/student/lessons/{$lesson->id}/quiz")->assertForbidden();
    }
}
