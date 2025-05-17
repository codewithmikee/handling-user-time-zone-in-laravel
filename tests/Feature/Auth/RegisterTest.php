<?php

declare(strict_types=1);

namespace Tests\Feature\Auth;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RegisterTest extends TestCase
{
    use RefreshDatabase;

    public function test_registers_user_with_valid_data(): void
    {
        $response = $this->postJson('/api/auth/register', [
            'name' => 'Test User',
            'email' => 'test@example.com',
            'password' => 'password123',
        ]);

        $response->assertStatus(200)
            ->assertJsonStructure([
                'success', 'message', 'data' => ['user', 'token'], 'errors',
            ]);
        $this->assertDatabaseHas('users', ['email' => 'test@example.com']);
    }

    public function test_fails_with_missing_fields(): void
    {
        $response = $this->postJson('/api/auth/register', [
            'email' => 'test@example.com',
        ]);
        $response->assertStatus(422)
            ->assertJson(['success' => false]);
    }

    public function test_fails_with_duplicate_email(): void
    {
        User::factory()->create(['email' => 'test@example.com']);
        $response = $this->postJson('/api/auth/register', [
            'name' => 'Test User',
            'email' => 'test@example.com',
            'password' => 'password123',
        ]);
        $response->assertStatus(422)
            ->assertJson(['success' => false]);
    }
}
