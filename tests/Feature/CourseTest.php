<?php

namespace Tests\Feature;

use App\Models\Course;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CourseTest extends TestCase
{
    use RefreshDatabase;

    public function test_public_course_listing_returns_published_courses(): void
    {
        Course::query()->create([
            'title_ar' => 'دورة منشورة',
            'slug' => 'published-course',
            'is_published' => true,
        ]);

        Course::query()->create([
            'title_ar' => 'دورة مخفية',
            'slug' => 'hidden-course',
            'is_published' => false,
        ]);

        $this->getJson('/api/v1/courses')
            ->assertOk()
            ->assertJsonCount(1, 'data')
            ->assertJsonPath('data.0.slug', 'published-course');
    }

    public function test_public_course_details_returns_published_course(): void
    {
        Course::query()->create([
            'title_ar' => 'دورة',
            'slug' => 'course-details',
            'is_published' => true,
        ]);

        $this->getJson('/api/v1/courses/course-details')
            ->assertOk()
            ->assertJsonPath('data.slug', 'course-details');
    }

    public function test_unpublished_course_is_not_accessible(): void
    {
        Course::query()->create([
            'title_ar' => 'دورة مخفية',
            'slug' => 'secret-course',
            'is_published' => false,
        ]);

        $this->getJson('/api/v1/courses/secret-course')->assertNotFound();
    }
}
