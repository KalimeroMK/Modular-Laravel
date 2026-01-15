<?php

declare(strict_types=1);

namespace Tests\Unit\Permission;

use App\Modules\Permission\Application\Actions\CreatePermissionAction;
use App\Modules\Permission\Application\DTO\CreatePermissionDTO;
use App\Modules\Permission\Infrastructure\Models\Permission;
use App\Modules\Permission\Infrastructure\Repositories\PermissionRepositoryInterface;
use Exception;
use Mockery;
use Override;
use Tests\TestCase;

class CreatePermissionActionTest extends TestCase
{
    #[Override]
    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }

    public function test_execute_successful_permission_creation(): void
    {
        // Arrange
        $name = 'manage-users';
        $guardName = 'api';

        $dto = new CreatePermissionDTO($name, $guardName);

        $permission = new Permission();
        $permission->id = 1;
        $permission->name = $name;
        $permission->guard_name = $guardName;

        $permissionRepository = Mockery::mock(PermissionRepositoryInterface::class);
        $permissionRepository->shouldReceive('create')
            ->with(Mockery::on(fn ($data) => $data['name'] === $name
                && $data['guard_name'] === $guardName))
            ->andReturn($permission);

        $action = new CreatePermissionAction($permissionRepository);

        // Act
        $result = $action->execute($dto);

        // Assert
        $this->assertInstanceOf(Permission::class, $result);
        $this->assertEquals($name, $result->name);
        $this->assertEquals($guardName, $result->guard_name);
    }

    public function test_execute_permission_creation_failure(): void
    {
        // Arrange
        $name = 'manage-users';
        $guardName = 'api';

        $dto = new CreatePermissionDTO($name, $guardName);

        $permissionRepository = Mockery::mock(PermissionRepositoryInterface::class);
        $permissionRepository->shouldReceive('create')
            ->andReturn(null);

        $action = new CreatePermissionAction($permissionRepository);

        // Act & Assert
        $this->expectException(Exception::class);
        $this->expectExceptionMessage('Failed to create permission');
        $action->execute($dto);
    }
}
