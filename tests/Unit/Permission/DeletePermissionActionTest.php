<?php

declare(strict_types=1);

namespace Tests\Unit\Permission;

use App\Modules\Permission\Application\Actions\DeletePermissionAction;
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

        $permissionId = 1;
        $permission = Mockery::mock(\App\Modules\Permission\Infrastructure\Models\Permission::class);

        $permissionRepository = Mockery::mock(PermissionRepositoryInterface::class);
        $permissionRepository->shouldReceive('findOrFail')->with($permissionId)->andReturn($permission);
        $permissionRepository->shouldReceive('delete')->with($permissionId)->andReturn(true);

        $queryBuilder = Mockery::mock();
        $queryBuilder->shouldReceive('where')->with('permission_id', $permissionId)->andReturnSelf();
        $queryBuilder->shouldReceive('delete')->andReturn(1);

        $queryBuilder2 = Mockery::mock();
        $queryBuilder2->shouldReceive('where')->with('permission_id', $permissionId)->andReturnSelf();
        $queryBuilder2->shouldReceive('delete')->andReturn(1);

        DB::shouldReceive('table')->with('role_has_permissions')->andReturn($queryBuilder);
        DB::shouldReceive('table')->with('model_has_permissions')->andReturn($queryBuilder2);

        $action = new DeletePermissionAction($permissionRepository);

        $result = $action->execute($permissionId);

        $this->assertTrue($result);
    }

    public function test_execute_permission_deletion_failure(): void
    {

        $permissionId = 1;
        $permission = Mockery::mock(\App\Modules\Permission\Infrastructure\Models\Permission::class);

        $permissionRepository = Mockery::mock(PermissionRepositoryInterface::class);
        $permissionRepository->shouldReceive('findOrFail')->with($permissionId)->andReturn($permission);
        $permissionRepository->shouldReceive('delete')->with($permissionId)->andReturn(false);

        $queryBuilder = Mockery::mock();
        $queryBuilder->shouldReceive('where')->with('permission_id', $permissionId)->andReturnSelf();
        $queryBuilder->shouldReceive('delete')->andReturn(1);

        $queryBuilder2 = Mockery::mock();
        $queryBuilder2->shouldReceive('where')->with('permission_id', $permissionId)->andReturnSelf();
        $queryBuilder2->shouldReceive('delete')->andReturn(1);

        DB::shouldReceive('table')->with('role_has_permissions')->andReturn($queryBuilder);
        DB::shouldReceive('table')->with('model_has_permissions')->andReturn($queryBuilder2);

        $action = new DeletePermissionAction($permissionRepository);

        $result = $action->execute($permissionId);

        $this->assertFalse($result);
    }
}
