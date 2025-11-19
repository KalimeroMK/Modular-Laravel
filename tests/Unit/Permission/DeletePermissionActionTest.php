<?php

declare(strict_types=1);

namespace Tests\Unit\Permission;

use App\Modules\Permission\Application\Actions\DeletePermissionAction;
use App\Modules\Permission\Infrastructure\Models\Permission;
use App\Modules\Permission\Infrastructure\Repositories\PermissionRepositoryInterface;
use Illuminate\Support\Facades\DB;
use Mockery;
use Tests\TestCase;

class DeletePermissionActionTest extends TestCase
{
    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }

    public function test_execute_successful_permission_deletion(): void
    {
        // Arrange
        $permission = Mockery::mock(Permission::class);
        $permission->shouldReceive('getKey')->andReturn(1);

        $permissionRepository = Mockery::mock(PermissionRepositoryInterface::class);

        // Mock DB query builder chain
        $queryBuilder = Mockery::mock();
        $queryBuilder->shouldReceive('where')->with('permission_id', 1)->andReturnSelf();
        $queryBuilder->shouldReceive('delete')->andReturn(1);

        $queryBuilder2 = Mockery::mock();
        $queryBuilder2->shouldReceive('where')->with('permission_id', 1)->andReturnSelf();
        $queryBuilder2->shouldReceive('delete')->andReturn(1);

        $queryBuilder3 = Mockery::mock();
        $queryBuilder3->shouldReceive('where')->with('id', 1)->andReturnSelf();
        $queryBuilder3->shouldReceive('delete')->andReturn(1);

        DB::shouldReceive('table')->with('role_has_permissions')->andReturn($queryBuilder);
        DB::shouldReceive('table')->with('model_has_permissions')->andReturn($queryBuilder2);
        DB::shouldReceive('table')->with('permissions')->andReturn($queryBuilder3);

        $action = new DeletePermissionAction($permissionRepository);

        // Act
        $result = $action->execute($permission);

        // Assert
        $this->assertTrue($result);
    }

    public function test_execute_permission_deletion_failure(): void
    {
        // Arrange
        $permission = Mockery::mock(Permission::class);
        $permission->shouldReceive('getKey')->andReturn(1);

        $permissionRepository = Mockery::mock(PermissionRepositoryInterface::class);

        // Mock DB query builder chain
        $queryBuilder = Mockery::mock();
        $queryBuilder->shouldReceive('where')->with('permission_id', 1)->andReturnSelf();
        $queryBuilder->shouldReceive('delete')->andReturn(1);

        $queryBuilder2 = Mockery::mock();
        $queryBuilder2->shouldReceive('where')->with('permission_id', 1)->andReturnSelf();
        $queryBuilder2->shouldReceive('delete')->andReturn(1);

        $queryBuilder3 = Mockery::mock();
        $queryBuilder3->shouldReceive('where')->with('id', 1)->andReturnSelf();
        $queryBuilder3->shouldReceive('delete')->andReturn(0); // No rows deleted

        DB::shouldReceive('table')->with('role_has_permissions')->andReturn($queryBuilder);
        DB::shouldReceive('table')->with('model_has_permissions')->andReturn($queryBuilder2);
        DB::shouldReceive('table')->with('permissions')->andReturn($queryBuilder3);

        $action = new DeletePermissionAction($permissionRepository);

        // Act
        $result = $action->execute($permission);

        // Assert
        $this->assertFalse($result);
    }
}
