<?php

declare(strict_types=1);

namespace Tests\Unit\Permission;

use App\Modules\Core\Exceptions\UpdateException;
use App\Modules\Permission\Application\Actions\UpdatePermissionAction;
use App\Modules\Permission\Application\DTO\UpdatePermissionDTO;
use App\Modules\Permission\Infrastructure\Models\Permission;
use App\Modules\Permission\Infrastructure\Repositories\PermissionRepositoryInterface;
use Mockery;
use Override;
use Tests\TestCase;

class UpdatePermissionActionTest extends TestCase
{
    #[Override]
    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }

    public function test_execute_successful_permission_update(): void
    {
        // Arrange
        $permissionId = 1;
        $name = 'updated-manage-users';
        $guardName = 'api';

        $dto = new UpdatePermissionDTO($name, $guardName);

        $permission = new Permission();
        $permission->id = $permissionId;
        $permission->name = 'manage-users';
        $permission->guard_name = 'api';

        $updatedPermission = new Permission();
        $updatedPermission->id = $permissionId;
        $updatedPermission->name = $name;
        $updatedPermission->guard_name = $guardName;

        $permissionRepository = Mockery::mock(PermissionRepositoryInterface::class);
        $permissionRepository->shouldReceive('findOrFail')->with($permissionId)->andReturn($permission);
        $permissionRepository->shouldReceive('update')
            ->with($permissionId, Mockery::on(fn ($data) => $data['name'] === $name
                && $data['guard_name'] === $guardName))
            ->andReturn($updatedPermission);

        $action = new UpdatePermissionAction($permissionRepository);

        // Act
        $result = $action->execute($permissionId, $dto);

        // Assert
        $this->assertInstanceOf(Permission::class, $result);
        $this->assertEquals($name, $result->name);
        $this->assertEquals($guardName, $result->guard_name);
    }

    public function test_execute_permission_update_failure(): void
    {
        // Arrange
        $permissionId = 1;
        $name = 'updated-manage-users';
        $guardName = 'api';

        $dto = new UpdatePermissionDTO($name, $guardName);

        $permission = new Permission();
        $permission->id = $permissionId;

        $permissionRepository = Mockery::mock(PermissionRepositoryInterface::class);
        $permissionRepository->shouldReceive('findOrFail')->with($permissionId)->andReturn($permission);
        $permissionRepository->shouldReceive('update')
            ->andReturn(null);

        $action = new UpdatePermissionAction($permissionRepository);

        // Act & Assert
        $this->expectException(UpdateException::class);
        $this->expectExceptionMessage('Failed to update permission');
        $action->execute($permissionId, $dto);
    }
}
