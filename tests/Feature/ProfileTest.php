<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class ProfileTest extends TestCase
{
    use RefreshDatabase;

    public function test_fetches_profile_for_authenticated_user(): void
    {
        $user = User::factory()->create([
            'name' => 'Test User',
            'email' => 'test@example.com',
        ]);
        $token = $user->createToken('test-token')->plainTextToken;

        Sanctum::actingAs($user);
        $response = $this->getJson('/api/profile/');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'success', 'message', 'data' => ['name', 'email'], 'errors',
            ])
            ->assertJson([
                'data' => [
                    'name' => 'Test User',
                    'email' => 'test@example.com',
                ],
            ]);
    }

    public function test_fails_for_unauthenticated_user(): void
    {
        $response = $this->getJson('/api/profile');
        $response->assertStatus(401)
            ->assertJson(['success' => false]);
    }
}
