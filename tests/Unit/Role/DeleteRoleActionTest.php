<?php

declare(strict_types=1);

namespace Tests\Unit\Role;

use App\Modules\Role\Application\Actions\DeleteRoleAction;
use App\Modules\Role\Infrastructure\Models\Role;
use App\Modules\Role\Infrastructure\Repositories\RoleRepositoryInterface;
use Illuminate\Support\Facades\DB;
use Mockery;
use Tests\TestCase;

class DeleteRoleActionTest extends TestCase
{
    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }

    public function test_execute_successful_role_deletion(): void
    {
        // Arrange
        $role = Mockery::mock(Role::class);
        $role->shouldReceive('getKey')->andReturn(1);

        $roleRepository = Mockery::mock(RoleRepositoryInterface::class);

        // Mock DB query builder chain
        $queryBuilder = Mockery::mock();
        $queryBuilder->shouldReceive('where')->with('role_id', 1)->andReturnSelf();
        $queryBuilder->shouldReceive('delete')->andReturn(1);

        $queryBuilder2 = Mockery::mock();
        $queryBuilder2->shouldReceive('where')->with('role_id', 1)->andReturnSelf();
        $queryBuilder2->shouldReceive('delete')->andReturn(1);

        $queryBuilder3 = Mockery::mock();
        $queryBuilder3->shouldReceive('where')->with('id', 1)->andReturnSelf();
        $queryBuilder3->shouldReceive('delete')->andReturn(1);

        DB::shouldReceive('table')->with('role_has_permissions')->andReturn($queryBuilder);
        DB::shouldReceive('table')->with('model_has_roles')->andReturn($queryBuilder2);
        DB::shouldReceive('table')->with('roles')->andReturn($queryBuilder3);

        $action = new DeleteRoleAction($roleRepository);

        // Act
        $result = $action->execute($role);

        // Assert
        $this->assertTrue($result);
    }

    public function test_execute_role_deletion_failure(): void
    {
        // Arrange
        $role = Mockery::mock(Role::class);
        $role->shouldReceive('getKey')->andReturn(1);

        $roleRepository = Mockery::mock(RoleRepositoryInterface::class);

        // Mock DB query builder chain
        $queryBuilder = Mockery::mock();
        $queryBuilder->shouldReceive('where')->with('role_id', 1)->andReturnSelf();
        $queryBuilder->shouldReceive('delete')->andReturn(1);

        $queryBuilder2 = Mockery::mock();
        $queryBuilder2->shouldReceive('where')->with('role_id', 1)->andReturnSelf();
        $queryBuilder2->shouldReceive('delete')->andReturn(1);

        $queryBuilder3 = Mockery::mock();
        $queryBuilder3->shouldReceive('where')->with('id', 1)->andReturnSelf();
        $queryBuilder3->shouldReceive('delete')->andReturn(0); // No rows deleted

        DB::shouldReceive('table')->with('role_has_permissions')->andReturn($queryBuilder);
        DB::shouldReceive('table')->with('model_has_roles')->andReturn($queryBuilder2);
        DB::shouldReceive('table')->with('roles')->andReturn($queryBuilder3);

        $action = new DeleteRoleAction($roleRepository);

        // Act
        $result = $action->execute($role);

        // Assert
        $this->assertFalse($result);
    }
}
