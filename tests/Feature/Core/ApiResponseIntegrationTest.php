<?php

declare(strict_types=1);

namespace Tests\Feature\Core;

use App\Modules\User\Infrastructure\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Override;
use Tests\TestCase;

class ApiResponseIntegrationTest extends TestCase
{
    use RefreshDatabase;

    public $user;

    #[Override]
    protected function setUp(): void
    {
        parent::setUp();
        $this->user = User::factory()->create();
        Sanctum::actingAs($this->user);
    }

    public function test_user_index_returns_paginated_response_structure(): void
    {
        User::factory()->count(5)->create();

        $response = $this->getJson('/api/v1/users');

        $response->assertStatus(200);
        $response->assertJsonStructure([
            'status',
            'message',
            'data',
            'meta' => [
                'current_page',
                'last_page',
                'per_page',
                'total',
                'from',
                'to',
            ],
            'links' => [
                'first',
                'last',
                'prev',
                'next',
            ],
        ]);

        $response->assertJson([
            'status' => 'success',
            'message' => 'Users retrieved successfully',
        ]);
    }

    public function test_user_show_returns_success_response_structure(): void
    {
        $user = User::factory()->create();

        $response = $this->getJson("/api/v1/users/{$user->id}");

        $response->assertStatus(200);
        $response->assertJsonStructure([
            'status',
            'message',
            'data',
        ]);

        $response->assertJson([
            'status' => 'success',
            'message' => 'User retrieved successfully',
        ]);
    }

    public function test_user_store_returns_created_response_structure(): void
    {
        $payload = [
            'name' => 'Test User',
            'email' => 'test@example.com',
            'password' => 'password123',
        ];

        $response = $this->postJson('/api/v1/users', $payload);

        $response->assertStatus(201);
        $response->assertJsonStructure([
            'status',
            'message',
            'data',
        ]);

        $response->assertJson([
            'status' => 'success',
            'message' => 'User created successfully',
        ]);
    }

    public function test_user_update_returns_success_response_structure(): void
    {
        $user = User::factory()->create();
        $payload = ['name' => 'Updated Name'];

        $response = $this->putJson("/api/v1/users/{$user->id}", $payload);

        $response->assertStatus(200);
        $response->assertJsonStructure([
            'status',
            'message',
            'data',
        ]);

        $response->assertJson([
            'status' => 'success',
            'message' => 'User updated successfully',
        ]);
    }

    public function test_user_destroy_returns_success_response_structure(): void
    {
        $user = User::factory()->create();

        $response = $this->deleteJson("/api/v1/users/{$user->id}");

        $response->assertStatus(200);
        $response->assertJsonStructure([
            'status',
            'message',
            'data',
        ]);

        $response->assertJson([
            'status' => 'success',
            'message' => 'User deleted successfully',
        ]);
    }

    public function test_role_index_returns_paginated_response(): void
    {
        $response = $this->getJson('/api/v1/roles');

        $response->assertStatus(200);
        $response->assertJson([
            'status' => 'success',
            'message' => 'Roles retrieved successfully',
        ]);
    }

    public function test_permission_index_returns_paginated_response(): void
    {
        $response = $this->getJson('/api/v1/permissions');

        $response->assertStatus(200);
        $response->assertJson([
            'status' => 'success',
            'message' => 'Permissions retrieved successfully',
        ]);
    }
}
