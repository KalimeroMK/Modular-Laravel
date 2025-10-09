<?php

declare(strict_types=1);

namespace Tests\Unit\Permission;

use App\Modules\Permission\Application\Actions\GetPermissionByIdAction;
use App\Modules\Permission\Application\DTO\PermissionResponseDTO;
use App\Modules\Permission\Infrastructure\Repositories\PermissionRepositoryInterface;
use Mockery;
use Spatie\Permission\Models\Permission;
use Tests\TestCase;

class GetPermissionByIdActionTest extends TestCase
{
    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }

    public function test_execute_returns_permission_when_found(): void
    {
        // Arrange
        $permissionId = 1;
        $permission = new Permission();
        $permission->id = $permissionId;
        $permission->name = 'manage-users';
        $permission->guard_name = 'web';

        $permissionRepository = Mockery::mock(PermissionRepositoryInterface::class);
        $permissionRepository->shouldReceive('find')
            ->with($permissionId)
            ->andReturn($permission);

        $action = new GetPermissionByIdAction($permissionRepository);

        // Act
        $result = $action->execute($permissionId);

        // Assert
        $this->assertInstanceOf(PermissionResponseDTO::class, $result);
        $this->assertEquals($permissionId, $result->id);
        $this->assertEquals('manage-users', $result->name);
        $this->assertEquals('web', $result->guardName);
    }

    public function test_execute_returns_null_when_permission_not_found(): void
    {
        // Arrange
        $permissionId = 999;

        $permissionRepository = Mockery::mock(PermissionRepositoryInterface::class);
        $permissionRepository->shouldReceive('find')
            ->with($permissionId)
            ->andReturn(null);

        $action = new GetPermissionByIdAction($permissionRepository);

        // Act
        $result = $action->execute($permissionId);

        // Assert
        $this->assertNull($result);
    }
}
