<?php

declare(strict_types=1);

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AuthModuleSmokeTest extends TestCase
{
    use RefreshDatabase;

    public function test_can_register_and_login(): void
    {
        $email = 'testuser_'.uniqid().'@example.com';
        $password = 'password123';

        $register = $this->postJson('/api/v1/auth/register', [
            'name' => 'Test User',
            'email' => $email,
            'password' => $password,
            'password_confirmation' => $password,
        ]);

        $register->assertStatus(201)->assertJsonStructure([
            'status',
            'data' => [
                'user' => ['id', 'name', 'email'],
                'token',
            ],
        ]);

        $login = $this->postJson('/api/v1/auth/login', [
            'email' => $email,
            'password' => $password,
        ]);

        $login->assertStatus(200)->assertJsonStructure([
            'status',
            'data' => [
                'user' => ['id', 'name', 'email'],
                'token',
            ],
        ]);
    }

    public function test_me_requires_auth(): void
    {
        $this->getJson('/api/v1/auth/me')->assertStatus(401);
    }

    public function test_logout_requires_auth(): void
    {
        $this->postJson('/api/v1/auth/logout')->assertStatus(401);
    }

    public function test_can_logout(): void
    {
        $email = 'logoutuser_'.uniqid().'@example.com';
        $password = 'password123';
        $register = $this->postJson('/api/v1/auth/register', [
            'name' => 'Logout User',
            'email' => $email,
            'password' => $password,
            'password_confirmation' => $password,
        ]);
        $token = $register->json('data.token');
        $this->postJson('/api/v1/auth/logout', [], ['Authorization' => 'Bearer '.$token])
            ->assertStatus(200)
            ->assertJson(['status' => 'success']);
    }

    public function test_can_get_me(): void
    {
        $email = 'meuser_'.uniqid().'@example.com';
        $password = 'password123';
        $register = $this->postJson('/api/v1/auth/register', [
            'name' => 'Me User',
            'email' => $email,
            'password' => $password,
            'password_confirmation' => $password,
        ]);
        $token = $register->json('data.token');
        $me = $this->getJson('/api/v1/auth/me', ['Authorization' => 'Bearer '.$token]);
        $me->assertStatus(200)
            ->assertJsonStructure([
                'status',
                'data' => ['id', 'name', 'email'],
            ]);
    }

    public function test_can_send_reset_link(): void
    {
        $email = 'reset_'.uniqid().'@example.com';
        // Register user first
        $this->postJson('/api/v1/auth/register', [
            'name' => 'Reset User',
            'email' => $email,
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ]);
        $response = $this->postJson('/api/v1/auth/forgot-password', [
            'email' => $email,
        ]);
        $response->assertStatus(200)
            ->assertJsonStructure([
                'status',
                'message',
            ]);
    }

    public function test_reset_password_validation(): void
    {
        $response = $this->postJson('api/v1/auth/forgot-password', []);
        $response->assertStatus(422);
    }
}
