<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class AuthTest extends TestCase
{
    use RefreshDatabase;

    public function test_google_login_requires_id_token(): void
    {
        $response = $this->postJson('/api/auth/google', []);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['id_token']);
    }

    public function test_google_login_fails_with_invalid_token(): void
    {
        Http::fake([
            'oauth2.googleapis.com/*' => Http::response(['error' => 'invalid_token'], 400),
        ]);

        $response = $this->postJson('/api/auth/google', [
            'id_token' => 'invalid_token_that_is_at_least_100_characters_long_to_pass_validation_' . str_repeat('x', 50),
        ]);

        $response->assertStatus(401);
    }

    public function test_google_login_creates_user_and_returns_token(): void
    {
        Http::fake([
            'oauth2.googleapis.com/*' => Http::response([
                'sub' => '123456789',
                'email' => 'test@gmail.com',
                'email_verified' => 'true',
                'name' => 'Test User',
                'picture' => 'https://example.com/avatar.jpg',
                'aud' => config('services.google.mobile_client_ids.android'),
                'iss' => 'accounts.google.com',
                'exp' => time() + 3600,
            ], 200),
        ]);

        $response = $this->postJson('/api/auth/google', [
            'id_token' => 'valid_token_' . str_repeat('x', 100),
        ]);

        $response->assertStatus(200)
            ->assertJsonStructure([
                'success',
                'data' => [
                    'token',
                    'user' => ['id', 'name', 'email'],
                ],
            ]);

        $this->assertDatabaseHas('users', [
            'email' => 'test@gmail.com',
            'google_id' => '123456789',
        ]);
    }

    public function test_logout_revokes_token(): void
    {
        $user = User::factory()->create();
        $token = $user->createToken('test')->plainTextToken;

        $response = $this->withHeader('Authorization', 'Bearer ' . $token)
            ->postJson('/api/auth/logout');

        $response->assertStatus(200);

        // Token should no longer work
        $this->withHeader('Authorization', 'Bearer ' . $token)
            ->getJson('/api/auth/me')
            ->assertStatus(401);
    }

    public function test_get_current_user(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user, 'sanctum')
            ->getJson('/api/auth/me');

        $response->assertStatus(200)
            ->assertJsonPath('data.id', $user->id)
            ->assertJsonPath('data.email', $user->email);
    }
}



