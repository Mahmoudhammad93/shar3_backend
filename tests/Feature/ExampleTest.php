<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ExampleTest extends TestCase
{
    use RefreshDatabase;

    public function test_home_endpoint_returns_successful_response(): void
    {
        $this->getJson('/api/v1/home')->assertOk();
    }
}
