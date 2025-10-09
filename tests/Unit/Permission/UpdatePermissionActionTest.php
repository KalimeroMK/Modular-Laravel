<?php

declare(strict_types=1);

namespace Tests\Unit\Permission;

use App\Modules\Permission\Application\Actions\UpdatePermissionAction;
use App\Modules\Permission\Application\DTO\PermissionResponseDTO;
use App\Modules\Permission\Application\DTO\UpdatePermissionDTO;
use App\Modules\Permission\Infrastructure\Repositories\PermissionRepositoryInterface;
use Exception;
use Mockery;
use Spatie\Permission\Models\Permission;
use Tests\TestCase;

class UpdatePermissionActionTest extends TestCase
{
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
        $guardName = 'web';
        
        $dto = new UpdatePermissionDTO($name, $guardName);
        
        $permission = new Permission();
        $permission->id = $permissionId;
        $permission->name = $name;
        $permission->guard_name = $guardName;

        $permissionRepository = Mockery::mock(PermissionRepositoryInterface::class);
        $permissionRepository->shouldReceive('update')
            ->with($permissionId, Mockery::on(function ($data) use ($name, $guardName) {
                return $data['name'] === $name 
                    && $data['guard_name'] === $guardName;
            }))
            ->andReturn($permission);

        $action = new UpdatePermissionAction($permissionRepository);

        // Act
        $result = $action->execute($permissionId, $dto);

        // Assert
        $this->assertInstanceOf(PermissionResponseDTO::class, $result);
        $this->assertEquals($name, $result->name);
        $this->assertEquals($guardName, $result->guardName);
    }

    public function test_execute_permission_update_failure(): void
    {
        // Arrange
        $permissionId = 1;
        $name = 'updated-manage-users';
        $guardName = 'web';
        
        $dto = new UpdatePermissionDTO($name, $guardName);

        $permissionRepository = Mockery::mock(PermissionRepositoryInterface::class);
        $permissionRepository->shouldReceive('update')
            ->andReturn(null);

        $action = new UpdatePermissionAction($permissionRepository);

        // Act & Assert
        $this->expectException(Exception::class);
        $this->expectExceptionMessage('Failed to update permission');
        $action->execute($permissionId, $dto);
    }
}
