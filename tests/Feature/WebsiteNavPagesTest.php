<?php

namespace Tests\Feature;

use App\Models\SiteSetting;
use App\Support\WebsiteNavPages;
use Database\Seeders\DatabaseSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class WebsiteNavPagesTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(DatabaseSeeder::class);
    }

    public function test_settings_api_returns_nav_pages(): void
    {
        $response = $this->getJson('/api/v1/settings');

        $response->assertOk()
            ->assertJsonStructure([
                'data' => [
                    'website_nav_pages' => [
                        '*' => ['href', 'label_ar', 'description_ar'],
                    ],
                ],
            ]);

        $this->assertGreaterThan(0, count($response->json('data.website_nav_pages')));
        $this->assertTrue(collect($response->json('data.website_nav_pages'))->every(
            fn (array $page) => array_key_exists('is_visible', $page),
        ));
    }

    public function test_hidden_page_is_marked_not_visible(): void
    {
        $pages = WebsiteNavPages::defaults();
        $pages[3]['is_visible'] = false;

        SiteSetting::current()->update([
            'website_nav_pages' => WebsiteNavPages::normalizeForStorage($pages),
        ]);

        $courses = collect($this->getJson('/api/v1/settings')->json('data.website_nav_pages'))
            ->firstWhere('href', '/courses');

        $this->assertFalse($courses['is_visible']);
    }

    public function test_nav_pages_can_be_reordered_and_updated(): void
    {
        $pages = WebsiteNavPages::defaults();
        $pages[1]['label_ar'] = 'عن المعهد المحدّث';
        $pages[1]['description_ar'] = 'وصف محدّث';
        $reordered = [$pages[1], $pages[0], ...array_slice($pages, 2)];

        SiteSetting::current()->update([
            'website_nav_pages' => WebsiteNavPages::normalizeForStorage($reordered),
        ]);

        $response = $this->getJson('/api/v1/settings');

        $response->assertOk()
            ->assertJsonPath('data.website_nav_pages.0.label_ar', 'عن المعهد المحدّث')
            ->assertJsonPath('data.website_nav_pages.0.description_ar', 'وصف محدّث')
            ->assertJsonPath('data.website_nav_pages.1.href', '/');
    }

    public function test_nav_pages_order_is_preserved_through_save_and_api(): void
    {
        $pages = WebsiteNavPages::defaults();
        $contact = collect($pages)->firstWhere('href', '/contact');
        $teachers = collect($pages)->firstWhere('href', '/teachers');
        $reordered = [
            $pages[0],
            $contact,
            $teachers,
            ...collect($pages)->reject(fn ($page) => in_array($page['href'], ['/', '/contact', '/teachers'], true))->values()->all(),
        ];

        SiteSetting::current()->update([
            'website_nav_pages' => WebsiteNavPages::normalizeForStorage($reordered),
        ]);

        $response = $this->getJson('/api/v1/settings');

        $response->assertOk()
            ->assertJsonPath('data.website_nav_pages.1.href', '/contact')
            ->assertJsonPath('data.website_nav_pages.2.href', '/teachers')
            ->assertJsonPath('data.website_nav_pages.1.sort_order', 1)
            ->assertJsonPath('data.website_nav_pages.2.sort_order', 2);
    }
}
