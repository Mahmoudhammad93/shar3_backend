<?php

namespace Tests\Feature;

use App\Models\Course;
use App\Models\Enrollment;
use App\Models\Lesson;
use App\Models\LessonProgress;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class LessonProgressTest extends TestCase
{
    use RefreshDatabase;

    private function enrolledLesson(bool $withVideo = true): array
    {
        $student = $this->actingAsStudent();
        $course = Course::query()->create([
            'title_ar' => 'دورة',
            'slug' => 'course-progress-'.uniqid(),
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
            'video_url' => $withVideo ? 'https://example.com/video.mp4' : null,
            'sort_order' => 1,
            'is_published' => true,
        ]);

        return [$student, $lesson];
    }

    public function test_student_can_update_own_lesson_progress(): void
    {
        [, $lesson] = $this->enrolledLesson();

        $this->postJson("/api/v1/student/lessons/{$lesson->id}/progress", [
            'progress_percent' => 50,
        ])->assertOk()->assertJsonPath('progress_percent', 50);
    }

    public function test_progress_does_not_move_backwards(): void
    {
        [$student, $lesson] = $this->enrolledLesson();

        LessonProgress::query()->create([
            'student_id' => $student->id,
            'lesson_id' => $lesson->id,
            'progress_percent' => 80,
        ]);

        $this->postJson("/api/v1/student/lessons/{$lesson->id}/progress", [
            'progress_percent' => 40,
        ])->assertOk()->assertJsonPath('progress_percent', 80);
    }

    public function test_complete_lesson_requires_video_progress(): void
    {
        [, $lesson] = $this->enrolledLesson();

        $this->postJson("/api/v1/student/lessons/{$lesson->id}/complete")
            ->assertStatus(422);
    }

    public function test_student_can_complete_lesson_after_video_progress(): void
    {
        [$student, $lesson] = $this->enrolledLesson(false);

        $this->postJson("/api/v1/student/lessons/{$lesson->id}/complete")->assertOk();

        $this->assertTrue(
            LessonProgress::query()
                ->where('student_id', $student->id)
                ->where('lesson_id', $lesson->id)
                ->value('is_completed')
        );
    }

    public function test_student_cannot_update_progress_for_unenrolled_lesson(): void
    {
        $this->actingAsStudent();

        $course = Course::query()->create([
            'title_ar' => 'دورة',
            'slug' => 'other-course',
            'is_published' => true,
        ]);

        $lesson = Lesson::query()->create([
            'course_id' => $course->id,
            'title_ar' => 'درس',
            'sort_order' => 1,
            'is_published' => true,
        ]);

        $this->postJson("/api/v1/student/lessons/{$lesson->id}/progress", [
            'progress_percent' => 50,
        ])->assertForbidden();
    }
}
