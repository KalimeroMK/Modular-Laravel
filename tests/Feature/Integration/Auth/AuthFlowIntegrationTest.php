<?php

declare(strict_types=1);

namespace Tests\Feature\Integration\Auth;

use App\Modules\User\Infrastructure\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class AuthFlowIntegrationTest extends TestCase
{
    use RefreshDatabase;

    public function test_complete_auth_flow(): void
    {

        $userData = [
            'name' => 'Test User',
            'email' => 'test@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ];

        $registerResponse = $this->postJson('/api/v1/auth/register', $userData);
        $registerResponse->assertStatus(201);
        $registerResponse->assertJsonStructure([
            'data' => [
                'user' => [
                    'id',
                    'name',
                    'email',
                ],
                'token',
            ],
        ]);

        $token = $registerResponse->json('data.token');

        $loginData = [
            'email' => 'test@example.com',
            'password' => 'password123',
        ];

        $loginResponse = $this->postJson('/api/v1/auth/login', $loginData);
        $loginResponse->assertStatus(200);
        $loginResponse->assertJsonStructure([
            'data' => [
                'user' => [
                    'id',
                    'name',
                    'email',
                ],
                'token',
            ],
        ]);

        $meResponse = $this->withHeaders([
            'Authorization' => 'Bearer '.$token,
        ])->getJson('/api/v1/auth/me');

        $meResponse->assertStatus(200);
        $meResponse->assertJsonStructure([
            'data' => [
                'id',
                'name',
                'email',
            ],
        ]);

        $logoutResponse = $this->withHeaders([
            'Authorization' => 'Bearer '.$token,
        ])->postJson('/api/v1/auth/logout');

        $logoutResponse->assertStatus(200);

        $this->withHeaders([
            'Authorization' => 'Bearer '.$token,
        ])->getJson('/api/v1/auth/me');

        $logoutResponse->assertStatus(200);
    }

    public function test_password_reset_flow(): void
    {

        User::factory()->create([
            'email' => 'test@example.com',
        ]);

        $resetRequestResponse = $this->postJson('/api/v1/auth/forgot-password', [
            'email' => 'test@example.com',
        ]);

        $resetRequestResponse->assertStatus(200);

        $resetResponse = $this->postJson('/api/v1/auth/reset-password', [
            'email' => 'test@example.com',
            'password' => 'newpassword123',
            'password_confirmation' => 'newpassword123',
            'token' => 'test-token',
        ]);

        $resetResponse->assertStatus(422);
    }

    public function test_user_role_permission_integration(): void
    {

        $user = User::factory()->create();

        $role = \App\Modules\Role\Infrastructure\Models\Role::create([
            'name' => 'admin',
            'guard_name' => 'api',
        ]);

        $permission = \App\Modules\Permission\Infrastructure\Models\Permission::create([
            'name' => 'manage-users',
            'guard_name' => 'api',
        ]);

        $user->assignRole($role);
        $user->givePermissionTo($permission);

        Sanctum::actingAs($user);

        $usersResponse = $this->getJson('/api/v1/users');
        $usersResponse->assertStatus(200);

        $rolesResponse = $this->getJson('/api/v1/roles');
        $rolesResponse->assertStatus(200);

        $permissionsResponse = $this->getJson('/api/v1/permissions');
        $permissionsResponse->assertStatus(200);
    }
}
