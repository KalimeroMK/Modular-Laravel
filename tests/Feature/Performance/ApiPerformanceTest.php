<?php

declare(strict_types=1);

namespace Tests\Feature\Performance;

use App\Modules\User\Infrastructure\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class ApiPerformanceTest extends TestCase
{
    use RefreshDatabase;

    public function test_auth_endpoints_performance(): void
    {
        
        $startTime = microtime(true);

        $this->postJson('/api/v1/auth/register', [
            'name' => 'Test User',
            'email' => 'test@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ]);

        $registrationTime = microtime(true) - $startTime;

        
        $this->assertLessThan(1.0, $registrationTime, 'Registration took too long');

        
        $startTime = microtime(true);

        $this->postJson('/api/v1/auth/login', [
            'email' => 'test@example.com',
            'password' => 'password123',
        ]);

        $loginTime = microtime(true) - $startTime;

        
        $this->assertLessThan(0.5, $loginTime, 'Login took too long');
    }

    public function test_user_endpoints_performance(): void
    {
        
        $user = User::factory()->create();
        Sanctum::actingAs($user);

        
        $startTime = microtime(true);

        $response = $this->getJson('/api/v1/users');

        $usersListTime = microtime(true) - $startTime;

        
        $this->assertLessThan(0.3, $usersListTime, 'Users list took too long');
        $response->assertStatus(200);

        
        $startTime = microtime(true);

        $this->postJson('/api/v1/users', [
            'name' => 'New User',
            'email' => 'newuser@example.com',
            'password' => 'password123',
        ]);

        $userCreationTime = microtime(true) - $startTime;

        
        $this->assertLessThan(0.5, $userCreationTime, 'User creation took too long');
    }

    public function test_role_permission_endpoints_performance(): void
    {
        
        $user = User::factory()->create();
        Sanctum::actingAs($user);

        
        $startTime = microtime(true);

        $response = $this->getJson('/api/v1/roles');

        $rolesListTime = microtime(true) - $startTime;

        
        $this->assertLessThan(0.3, $rolesListTime, 'Roles list took too long');
        $response->assertStatus(200);

        
        $startTime = microtime(true);

        $response = $this->getJson('/api/v1/permissions');

        $permissionsListTime = microtime(true) - $startTime;

        
        $this->assertLessThan(0.3, $permissionsListTime, 'Permissions list took too long');
        $response->assertStatus(200);
    }

    public function test_concurrent_requests_performance(): void
    {
        
        $user = User::factory()->create();
        Sanctum::actingAs($user);

        $startTime = microtime(true);

        
        $responses = [];
        for ($i = 0; $i < 10; $i++) {
            $responses[] = $this->getJson('/api/v1/users');
        }

        $concurrentTime = microtime(true) - $startTime;

        
        $this->assertLessThan(2.0, $concurrentTime, 'Concurrent requests took too long');

        
        foreach ($responses as $response) {
            $response->assertStatus(200);
        }
    }

    public function test_memory_usage(): void
    {
        $initialMemory = memory_get_usage();

        
        User::factory()->count(100)->create();

        $afterCreationMemory = memory_get_usage();
        $memoryIncrease = $afterCreationMemory - $initialMemory;

        
        $this->assertLessThan(10 * 1024 * 1024, $memoryIncrease, 'Memory usage too high');

        
        $user = User::first();
        Sanctum::actingAs($user);

        $startTime = microtime(true);
        $this->getJson('/api/v1/users');
        $responseTime = microtime(true) - $startTime;

        
        $this->assertLessThan(0.5, $responseTime, 'Response time degraded with more data');
    }
}
