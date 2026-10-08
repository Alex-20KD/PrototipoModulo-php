<?php

namespace Tests\Feature\Api;

use App\Enums\StaffRole;
use App\Models\Staff;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\RateLimiter;
use Tests\TestCase;

class AuthApiTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        // Asegurarse de que el limitador de tasa este limpio
        RateLimiter::clear('test@example.com|127.0.0.1');
    }

    public function test_can_login_with_valid_credentials(): void
    {
        $staff = Staff::create([
            'name' => 'John Doe',
            'email' => 'test@example.com',
            'password' => Hash::make('password123'),
            'role' => StaffRole::Reception,
        ]);

        $response = $this->postJson('/api/auth/login', [
            'email' => 'test@example.com',
            'password' => 'password123',
        ]);

        $response->assertStatus(200)
            ->assertJsonStructure([
                'status',
                'message',
                'data' => ['token', 'role'],
            ])
            ->assertJsonPath('status', true);

        $this->assertNotNull($response->json('data.token'));
    }

    public function test_cannot_login_with_invalid_credentials(): void
    {
        $staff = Staff::create([
            'name' => 'John Doe',
            'email' => 'test@example.com',
            'password' => Hash::make('password123'),
            'role' => StaffRole::Reception,
        ]);

        $response = $this->postJson('/api/auth/login', [
            'email' => 'test@example.com',
            'password' => 'wrongpassword',
        ]);

        $response->assertStatus(401)
            ->assertJsonStructure([
                'status',
                'message',
                'data',
            ])
            ->assertJsonPath('status', false)
            ->assertJsonPath('message', 'Credenciales incorrectas');
    }

    public function test_login_is_rate_limited(): void
    {
        $staff = Staff::create([
            'name' => 'John Doe',
            'email' => 'test@example.com',
            'password' => Hash::make('password123'),
            'role' => StaffRole::Reception,
        ]);

        // Hit the endpoint 5 times with wrong credentials to reach the limit
        for ($i = 0; $i < 5; $i++) {
            $this->postJson('/api/auth/login', [
                'email' => 'test@example.com',
                'password' => 'wrongpassword',
            ]);
        }

        // The 6th time should return 429
        $response = $this->postJson('/api/auth/login', [
            'email' => 'test@example.com',
            'password' => 'wrongpassword',
        ]);

        $response->assertStatus(429)
            ->assertJsonStructure([
                'status',
                'message',
                'data',
            ])
            ->assertJsonPath('status', false);
    }

    public function test_can_get_user_profile(): void
    {
        $staff = Staff::create([
            'name' => 'John Doe',
            'email' => 'test@example.com',
            'password' => Hash::make('password123'),
            'role' => StaffRole::Reception,
        ]);

        $token = $staff->createToken('auth_token')->plainTextToken;

        $response = $this->withHeader('Authorization', 'Bearer '.$token)
            ->getJson('/api/auth/me');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'status',
                'message',
                'data' => [
                    'id',
                    'name',
                    'email',
                    'role',
                ],
            ])
            ->assertJsonPath('data.email', 'test@example.com');
    }

    public function test_profile_requires_authentication(): void
    {
        $response = $this->getJson('/api/auth/me');

        $response->assertStatus(401)
            ->assertJsonPath('status', false)
            ->assertJsonPath('message', 'No autenticado');
    }

    public function test_can_logout(): void
    {
        $staff = Staff::create([
            'name' => 'John Doe',
            'email' => 'test@example.com',
            'password' => Hash::make('password123'),
            'role' => StaffRole::Reception,
        ]);

        $token = $staff->createToken('auth_token')->plainTextToken;

        $response = $this->withHeader('Authorization', 'Bearer '.$token)
            ->postJson('/api/auth/logout');

        $response->assertStatus(200)
            ->assertJsonPath('status', true);

        // Check token was deleted
        $this->assertCount(0, $staff->tokens);
    }

    public function test_can_logout_all(): void
    {
        $staff = Staff::create([
            'name' => 'John Doe',
            'email' => 'test@example.com',
            'password' => Hash::make('password123'),
            'role' => StaffRole::Reception,
        ]);

        $staff->createToken('device_1')->plainTextToken;
        $token2 = $staff->createToken('device_2')->plainTextToken;

        $this->assertCount(2, $staff->tokens);

        $response = $this->withHeader('Authorization', 'Bearer '.$token2)
            ->postJson('/api/auth/logout-all');

        $response->assertStatus(200)
            ->assertJsonPath('status', true);

        // Check all tokens were deleted
        $staff->refresh();
        $this->assertCount(0, $staff->tokens);
    }
}
