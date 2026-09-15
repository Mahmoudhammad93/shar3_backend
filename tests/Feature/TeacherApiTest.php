<?php

namespace Tests\Feature;

use App\Models\Teacher;
use Database\Seeders\DatabaseSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TeacherApiTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(DatabaseSeeder::class);
    }

    public function test_home_returns_only_featured_teachers(): void
    {
        Teacher::query()->create([
            'name_ar' => 'معلم غير مميز',
            'slug' => 'not-featured',
            'is_active' => true,
            'is_featured' => false,
            'sort_order' => 99,
        ]);

        $response = $this->getJson('/api/v1/home');

        $response->assertOk();

        $names = collect($response->json('teachers'))->pluck('name_ar')->all();
        $this->assertNotContains('معلم غير مميز', $names);
        $this->assertGreaterThan(0, count($names));
    }

    public function test_teachers_index_can_filter_featured(): void
    {
        Teacher::query()->create([
            'name_ar' => 'معلم غير مميز',
            'slug' => 'not-featured-index',
            'is_active' => true,
            'is_featured' => false,
            'sort_order' => 99,
        ]);

        $response = $this->getJson('/api/v1/teachers?featured=1');

        $response->assertOk();

        $names = collect($response->json('data'))->pluck('name_ar')->all();
        $this->assertNotContains('معلم غير مميز', $names);
        $this->assertLessThanOrEqual(4, count($names));
    }
}
