<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Modules\Permission\Infrastructure\Models\Permission;
use App\Modules\Role\Infrastructure\Models\Role;
use App\Modules\User\Infrastructure\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RolePermissionModuleTest extends TestCase
{
    use RefreshDatabase;

    public function test_role_can_be_created()
    {
        $response = $this->postJson('/api/v1/roles', [
            'name' => 'admin',
        ]);
        $response->assertStatus(200);
        $this->assertDatabaseHas('roles', ['name' => 'admin']);
    }

    public function test_permission_can_be_created()
    {
        $response = $this->postJson('/api/v1/permissions', [
            'name' => 'edit-posts',
        ]);
        $response->assertStatus(200);
        $this->assertDatabaseHas('permissions', ['name' => 'edit-posts']);
    }

    public function test_permission_can_be_assigned_to_role()
    {
        $role = Role::create(['name' => 'Admin', 'guard_name' => 'web']);
        $permission = Permission::create(['name' => 'edit', 'guard_name' => 'web']);
        $role->givePermissionTo($permission);
        $this->assertTrue(
            $role->permissions()->where('permissions.id', $permission->id)->exists()
        );
    }

    public function test_role_can_be_assigned_to_user()
    {
        $user = User::factory()->create();
        $role = Role::factory()->create([
            'name' => 'assignable-role',
        ]);

        $user->assignRole($role);

        $this->assertTrue(
            $user->roles()->where('roles.id', $role->id)->exists()
        );
    }
}
