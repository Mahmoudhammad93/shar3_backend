<?php

namespace Tests\Feature;

use App\Models\Course;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class FixCourseSlugsCommandTest extends TestCase
{
    use RefreshDatabase;

    public function test_command_regenerates_mismatched_course_slug_from_title(): void
    {
        $course = Course::query()->create([
            'title_ar' => 'الفصول في سيرة الرسول',
            'slug' => 'first-year-semester-1',
            'is_published' => true,
        ]);

        $this->artisan('courses:fix-slugs')->assertSuccessful();

        $this->assertDatabaseHas('courses', [
            'id' => $course->id,
            'slug' => 'alfsol-fy-syr-alrsol',
        ]);
    }
}
