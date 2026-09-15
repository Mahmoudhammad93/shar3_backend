<?php

namespace Tests\Feature;

use App\Models\Subject;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SubjectSlugTest extends TestCase
{
    use RefreshDatabase;

    public function test_subject_slug_is_normalized_and_made_unique_on_create(): void
    {
        Subject::query()->create([
            'name_ar' => 'حديث',
            'slug' => 'hadeeth',
            'is_active' => true,
        ]);

        $duplicate = Subject::query()->create([
            'name_ar' => 'منهج البخاري ومسلم',
            'slug' => 'hadeeth ',
            'is_active' => true,
        ]);

        $this->assertSame('hadeeth-1', $duplicate->slug);
    }
}
