<?php

declare(strict_types=1);

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ApiRootTest extends TestCase
{
    public function test_api_root_returns_hello_message(): void
    {
        $response = $this->getJson('/api/');
        $response->assertStatus(200)
            ->assertJsonStructure([
                'success', 'message', 'data', 'errors'
            ])
            ->assertJson([
                'message' => 'Hello, Laravel API!'
            ]);
    }
}
