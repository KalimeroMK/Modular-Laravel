<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Modules\Role\Infrastructure\Models\Role;
use App\Modules\User\Infrastructure\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class RoleModuleCrudTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $user = User::factory()->create();
        Sanctum::actingAs($user);
    }

    public function test_can_create_role(): void
    {
        $payload = [
            'name' => 'Test Role '.uniqid(),
        ];
        $response = $this->postJson('/api/v1/roles', $payload);
        $response->assertStatus(201)
            ->assertJsonStructure([
                'status',
                'message',
                'data' => ['id', 'name'],
            ])
            ->assertJson([
                'status' => 'success',
                'message' => 'Role created successfully',
            ]);
        $this->assertDatabaseHas('roles', ['name' => $payload['name']]);
    }

    public function test_can_list_roles(): void
    {
        Role::factory()->count(2)->create();
        $response = $this->getJson('/api/v1/roles');
        $response->assertStatus(200)
            ->assertJsonStructure([
                'status',
                'message',
                'data',
                'meta',
                'links',
            ])
            ->assertJson([
                'status' => 'success',
                'message' => 'Roles retrieved successfully',
            ]);
    }

    public function test_can_show_role(): void
    {
        $role = Role::factory()->create();
        $response = $this->getJson("/api/v1/roles/{$role->id}");
        $response->assertStatus(200)
            ->assertJsonStructure([
                'status',
                'message',
                'data' => ['id', 'name'],
            ])
            ->assertJson([
                'status' => 'success',
                'message' => 'Role retrieved successfully',
            ]);
    }

    public function test_can_update_role(): void
    {
        $role = Role::factory()->create();
        $payload = ['name' => 'Updated Role '.uniqid()];
        $response = $this->putJson("/api/v1/roles/{$role->id}", $payload);
        $response->assertStatus(200)
            ->assertJsonStructure([
                'status',
                'message',
                'data' => ['id', 'name'],
            ])
            ->assertJson([
                'status' => 'success',
                'message' => 'Role updated successfully',
            ]);
        $this->assertDatabaseHas('roles', ['id' => $role->id, 'name' => $payload['name']]);
    }

    public function test_can_delete_role(): void
    {
        $role = Role::factory()->create();
        $response = $this->deleteJson("/api/v1/roles/{$role->id}");
        $response->assertStatus(200)
            ->assertJsonStructure([
                'status',
                'message',
                'data',
            ])
            ->assertJson([
                'status' => 'success',
                'message' => 'Role deleted successfully',
            ]);
        $this->assertDatabaseMissing('roles', ['id' => $role->id]);
    }
}
