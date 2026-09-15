<?php

namespace Tests\Feature;

use App\Models\Course;
use App\Models\SiteSetting;
use App\Support\HomepageFeaturedCourses;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class HomepageFeaturedCoursesTest extends TestCase
{
    use RefreshDatabase;

    public function test_homepage_hides_featured_courses_by_default(): void
    {
        Course::query()->create([
            'title_ar' => 'دورة مميزة',
            'slug' => 'featured-course',
            'is_published' => true,
            'is_featured' => true,
        ]);

        $response = $this->getJson('/api/v1/home');

        $response->assertOk()
            ->assertJsonPath('homepage_featured_courses_visible', false)
            ->assertJsonCount(0, 'featured_courses');
    }

    public function test_homepage_shows_featured_courses_when_enabled(): void
    {
        SiteSetting::current()->update([
            'homepage_featured_courses_enabled' => true,
        ]);

        Course::query()->create([
            'title_ar' => 'دورة مميزة ١',
            'slug' => 'featured-course-1',
            'is_published' => true,
            'is_featured' => true,
        ]);

        Course::query()->create([
            'title_ar' => 'دورة مميزة ٢',
            'slug' => 'featured-course-2',
            'is_published' => true,
            'is_featured' => true,
        ]);

        Course::query()->create([
            'title_ar' => 'دورة عادية',
            'slug' => 'regular-course',
            'is_published' => true,
            'is_featured' => false,
        ]);

        $response = $this->getJson('/api/v1/home');

        $response->assertOk()
            ->assertJsonPath('homepage_featured_courses_visible', true)
            ->assertJsonCount(2, 'featured_courses');
    }

    public function test_homepage_respects_featured_courses_schedule(): void
    {
        SiteSetting::current()->update([
            'homepage_featured_courses_enabled' => true,
            'homepage_featured_courses_visible_from' => now()->addDay(),
            'homepage_featured_courses_visible_until' => now()->addWeek(),
        ]);

        Course::query()->create([
            'title_ar' => 'دورة مميزة',
            'slug' => 'featured-course',
            'is_published' => true,
            'is_featured' => true,
        ]);

        $this->getJson('/api/v1/home')
            ->assertOk()
            ->assertJsonPath('homepage_featured_courses_visible', false)
            ->assertJsonCount(0, 'featured_courses');

        $this->assertTrue(
            HomepageFeaturedCourses::shouldShow(
                SiteSetting::current()->fresh(),
                now()->addDays(2),
            ),
        );

        $this->assertFalse(
            HomepageFeaturedCourses::shouldShow(
                SiteSetting::current()->fresh(),
                now()->addWeeks(2),
            ),
        );
    }
}
