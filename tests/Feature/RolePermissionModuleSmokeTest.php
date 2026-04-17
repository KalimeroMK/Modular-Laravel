<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Modules\Permission\Infrastructure\Models\Permission;
use App\Modules\User\Infrastructure\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class RolePermissionModuleSmokeTest extends TestCase
{
    use RefreshDatabase;

    public function test_permissions_and_roles_routes_exist()
    {
        $user = User::factory()->create();
        Permission::factory()->create(['name' => 'manage-roles', 'guard_name' => 'api']);
        Permission::factory()->create(['name' => 'manage-permissions', 'guard_name' => 'api']);
        $user->givePermissionTo(['manage-roles', 'manage-permissions']);
        Sanctum::actingAs($user);

        $this->getJson('/api/v1/permissions')->assertStatus(200);
        $this->getJson('/api/v1/roles')->assertStatus(200);
    }
}
