<?php

declare(strict_types=1);

namespace Tests\Unit\Permission;

use App\Modules\Permission\Application\Actions\DeletePermissionAction;
use App\Modules\Permission\Infrastructure\Models\Permission;
use App\Modules\Permission\Infrastructure\Repositories\PermissionRepositoryInterface;
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
        $permission = new Permission();
        $permission->id = 1;

        $permissionRepository = Mockery::mock(PermissionRepositoryInterface::class);
        $permissionRepository->shouldReceive('delete')
            ->with(1)
            ->andReturn(true);

        $action = new DeletePermissionAction($permissionRepository);

        // Act
        $result = $action->execute($permission);

        // Assert
        $this->assertTrue($result);
    }

    public function test_execute_permission_deletion_failure(): void
    {
        // Arrange
        $permission = new Permission();
        $permission->id = 1;

        $permissionRepository = Mockery::mock(PermissionRepositoryInterface::class);
        $permissionRepository->shouldReceive('delete')
            ->with(1)
            ->andReturn(false);

        $action = new DeletePermissionAction($permissionRepository);

        // Act
        $result = $action->execute($permission);

        // Assert
        $this->assertFalse($result);
    }
}
