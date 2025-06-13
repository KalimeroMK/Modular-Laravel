<?php

declare(strict_types=1);

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RolePermissionModuleSmokeTest extends TestCase
{
    use RefreshDatabase;

    public function test_permissions_and_roles_routes_exist()
    {
        $this->getJson('/api/v1/permissions')->assertStatus(200);
        $this->getJson('/api/v1/roles')->assertStatus(200);
    }
}
