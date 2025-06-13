<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Modules\Role\Models\Role;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RoleModuleCrudTest extends TestCase
{
    use RefreshDatabase;

    public function test_can_create_role(): void
    {
        $payload = [
            'name' => 'Test Role '.uniqid(),
        ];
        $response = $this->postJson('/api/v1/roles', $payload);
        $response->assertStatus(200)
            ->assertJsonStructure(['data' => ['id', 'name']]);
        $this->assertDatabaseHas('roles', ['name' => $payload['name']]);
    }

    public function test_can_list_roles(): void
    {
        Role::factory()->count(2)->create();
        $response = $this->getJson('/api/v1/roles');
        $response->assertStatus(200)
            ->assertJsonStructure(['data' => [['id', 'name']]]);
    }

    public function test_can_show_role(): void
    {
        $role = Role::factory()->create();
        $response = $this->getJson("/api/v1/roles/{$role->id}");
        $response->assertStatus(200)
            ->assertJsonStructure(['data' => ['id', 'name']]);
    }

    public function test_can_update_role(): void
    {
        $role = Role::factory()->create();
        $payload = ['name' => 'Updated Role '.uniqid()];
        $response = $this->putJson("/api/v1/roles/{$role->id}", $payload);
        $response->assertStatus(200)
            ->assertJsonStructure(['data' => ['id', 'name']]);
        $this->assertDatabaseHas('roles', ['id' => $role->id, 'name' => $payload['name']]);
    }

    public function test_can_delete_role(): void
    {
        $role = Role::factory()->create();
        $response = $this->deleteJson("/api/v1/roles/{$role->id}");
        $response->assertStatus(200)
            ->assertJson(['message' => 'Role deleted']);
        $this->assertDatabaseMissing('roles', ['id' => $role->id]);
    }
}
