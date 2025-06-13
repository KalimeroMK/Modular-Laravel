<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Modules\Permission\Models\Permission;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PermissionModuleCrudTest extends TestCase
{
    use RefreshDatabase;

    public function test_can_create_permission(): void
    {
        $payload = [
            'name' => 'Test Permission '.uniqid(),
            'guard_name' => 'api',
        ];
        $response = $this->postJson('/api/v1/permissions', $payload);
        $response->assertStatus(200)
            ->assertJsonStructure(['data' => ['id', 'name', 'guard_name']]);
        $this->assertDatabaseHas('permissions', ['name' => $payload['name'], 'guard_name' => 'web']);
    }

    public function test_can_list_permissions(): void
    {
        Permission::factory()->count(2)->create(['guard_name' => 'web']);
        $response = $this->getJson('/api/v1/permissions');
        $response->assertStatus(200)
            ->assertJsonStructure(['data' => [['id', 'name', 'guard_name']]]);
    }

    public function test_can_show_permission(): void
    {
        $permission = Permission::factory()->create(['guard_name' => 'web']);
        $response = $this->getJson("/api/v1/permissions/{$permission->id}");
        $response->assertStatus(200)
            ->assertJsonStructure(['data' => ['id', 'name', 'guard_name']]);
    }

    public function test_can_update_permission(): void
    {
        $permission = Permission::factory()->create(['guard_name' => 'web']);
        $payload = ['name' => 'Updated Permission '.uniqid(), 'guard_name' => 'web'];
        $response = $this->putJson("/api/v1/permissions/{$permission->id}", $payload);
        $response->assertStatus(200)
            ->assertJsonStructure(['data' => ['id', 'name', 'guard_name']]);
        $this->assertDatabaseHas('permissions', ['id' => $permission->id, 'name' => $payload['name'], 'guard_name' => 'web']);
    }

    public function test_can_delete_permission(): void
    {
        $permission = Permission::factory()->create(['guard_name' => 'web']);
        $response = $this->deleteJson("/api/v1/permissions/{$permission->id}");
        $response->assertStatus(200)
            ->assertJson(['message' => 'Permission deleted']);
        $this->assertDatabaseMissing('permissions', ['id' => $permission->id]);
    }
}
