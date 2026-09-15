<?php

namespace Tests\Feature;

use Database\Seeders\AcademicStructureSeeder;
use Database\Seeders\DatabaseSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProgramApiTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(DatabaseSeeder::class);
        $this->seed(AcademicStructureSeeder::class);
    }

    public function test_program_show_returns_level_subjects_not_courses(): void
    {
        $response = $this->getJson('/api/v1/programs/preparatory-program');

        $response->assertOk()
            ->assertJsonPath('data.slug', 'preparatory-program')
            ->assertJsonMissingPath('data.courses')
            ->assertJsonStructure([
                'data' => [
                    'id',
                    'name_ar',
                    'slug',
                    'subjects_count',
                    'years' => [
                        '*' => [
                            'id',
                            'name_ar',
                            'slug',
                            'semesters' => [
                                '*' => [
                                    'id',
                                    'name_ar',
                                    'slug',
                                    'subjects' => [
                                        '*' => [
                                            'id',
                                            'name_ar',
                                            'slug',
                                            'primary_text_ar',
                                            'supplementary_text_ar',
                                        ],
                                    ],
                                ],
                            ],
                        ],
                    ],
                    'subjects' => [
                        '*' => [
                            'id',
                            'name_ar',
                            'slug',
                        ],
                    ],
                ],
            ]);

        $this->assertGreaterThan(0, $response->json('data.subjects_count'));
        $this->assertGreaterThan(0, count($response->json('data.years')));
        $this->assertContains(
            'التوحيد',
            collect($response->json('data.subjects'))->pluck('name_ar')->all(),
        );
        $this->assertContains(
            'التوحيد',
            collect($response->json('data.years.0.semesters.0.subjects'))->pluck('name_ar')->all(),
        );
    }

    public function test_specialization_program_returns_subjects_grouped_by_specialization(): void
    {
        $response = $this->getJson('/api/v1/programs/specialization-program');

        $response->assertOk()
            ->assertJsonPath('data.slug', 'specialization-program')
            ->assertJsonMissingPath('data.courses')
            ->assertJsonStructure([
                'data' => [
                    'specializations' => [
                        '*' => [
                            'id',
                            'name_ar',
                            'slug',
                            'years' => [
                                '*' => [
                                    'id',
                                    'name_ar',
                                    'slug',
                                    'semesters' => [
                                        '*' => [
                                            'id',
                                            'name_ar',
                                            'slug',
                                            'subjects' => [
                                                '*' => ['id', 'name_ar', 'slug'],
                                            ],
                                        ],
                                    ],
                                ],
                            ],
                            'subjects' => [
                                '*' => ['id', 'name_ar', 'slug'],
                            ],
                        ],
                    ],
                ],
            ]);

        $this->assertGreaterThan(0, $response->json('data.subjects_count'));
        $this->assertEmpty($response->json('data.subjects') ?? []);
    }
}
