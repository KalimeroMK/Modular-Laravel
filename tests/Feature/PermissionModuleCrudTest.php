<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Modules\Permission\Infrastructure\Models\Permission;
use App\Modules\User\Infrastructure\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class PermissionModuleCrudTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $user = User::factory()->create();
        Sanctum::actingAs($user);
    }

    public function test_can_create_permission(): void
    {
        $payload = [
            'name' => 'Test Permission '.uniqid(),
            'guard_name' => 'web',
        ];
        $response = $this->postJson('/api/v1/permissions', $payload);
        $response->assertStatus(201)
            ->assertJsonStructure([
                'status',
                'message',
                'data' => ['id', 'name', 'guard_name'],
            ])
            ->assertJson([
                'status' => 'success',
                'message' => 'Permission created successfully',
            ]);
        $this->assertDatabaseHas('permissions', ['name' => $payload['name'], 'guard_name' => 'web']);
    }

    public function test_can_list_permissions(): void
    {
        Permission::factory()->count(2)->create(['guard_name' => 'web']);
        $response = $this->getJson('/api/v1/permissions');
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
                'message' => 'Permissions retrieved successfully',
            ]);
    }

    public function test_can_show_permission(): void
    {
        $permission = Permission::factory()->create(['guard_name' => 'web']);
        $response = $this->getJson("/api/v1/permissions/{$permission->id}");
        $response->assertStatus(200)
            ->assertJsonStructure([
                'status',
                'message',
                'data' => ['id', 'name', 'guard_name'],
            ])
            ->assertJson([
                'status' => 'success',
                'message' => 'Permission retrieved successfully',
            ]);
    }

    public function test_can_update_permission(): void
    {
        $permission = Permission::factory()->create(['guard_name' => 'web']);
        $payload = ['name' => 'Updated Permission '.uniqid(), 'guard_name' => 'web'];
        $response = $this->putJson("/api/v1/permissions/{$permission->id}", $payload);
        $response->assertStatus(200)
            ->assertJsonStructure([
                'status',
                'message',
                'data' => ['id', 'name', 'guard_name'],
            ])
            ->assertJson([
                'status' => 'success',
                'message' => 'Permission updated successfully',
            ]);
        $this->assertDatabaseHas('permissions', ['id' => $permission->id, 'name' => $payload['name'], 'guard_name' => 'web']);
    }

    public function test_can_delete_permission(): void
    {
        $permission = Permission::factory()->create(['guard_name' => 'web']);
        $response = $this->deleteJson("/api/v1/permissions/{$permission->id}");
        $response->assertStatus(200)
            ->assertJsonStructure([
                'status',
                'message',
                'data',
            ])
            ->assertJson([
                'status' => 'success',
                'message' => 'Permission deleted successfully',
            ]);
        $this->assertDatabaseMissing('permissions', ['id' => $permission->id]);
    }
}
