<?php

declare(strict_types=1);

namespace Tests\Unit\Role;

use App\Modules\Core\Exceptions\UpdateException;
use App\Modules\Role\Application\Actions\UpdateRoleAction;
use App\Modules\Role\Application\DTO\UpdateRoleDTO;
use App\Modules\Role\Infrastructure\Models\Role;
use App\Modules\Role\Infrastructure\Repositories\RoleRepositoryInterface;
use Mockery;
use Tests\TestCase;

class UpdateRoleActionTest extends TestCase
{
    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }

    public function test_execute_successful_role_update(): void
    {
        // Arrange
        $roleId = 1;
        $name = 'updated-admin';
        $guardName = 'api';

        $dto = new UpdateRoleDTO($name, $guardName);

        $role = new Role();
        $role->id = $roleId;
        $role->name = 'admin';
        $role->guard_name = 'api';

        $updatedRole = new Role();
        $updatedRole->id = $roleId;
        $updatedRole->name = $name;
        $updatedRole->guard_name = $guardName;

        $roleRepository = Mockery::mock(RoleRepositoryInterface::class);
        $roleRepository->shouldReceive('findOrFail')->with($roleId)->andReturn($role);
        $roleRepository->shouldReceive('update')
            ->with($roleId, Mockery::on(function ($data) use ($name, $guardName) {
                return $data['name'] === $name
                    && $data['guard_name'] === $guardName;
            }))
            ->andReturn($updatedRole);

        $action = new UpdateRoleAction($roleRepository);

        // Act
        $result = $action->execute($roleId, $dto);

        // Assert
        $this->assertInstanceOf(Role::class, $result);
        $this->assertEquals($name, $result->name);
        $this->assertEquals($guardName, $result->guard_name);
    }

    public function test_execute_role_update_failure(): void
    {
        // Arrange
        $roleId = 1;
        $name = 'updated-admin';
        $guardName = 'api';

        $dto = new UpdateRoleDTO($name, $guardName);

        $role = new Role();
        $role->id = $roleId;

        $roleRepository = Mockery::mock(RoleRepositoryInterface::class);
        $roleRepository->shouldReceive('findOrFail')->with($roleId)->andReturn($role);
        $roleRepository->shouldReceive('update')
            ->andReturn(null);

        $action = new UpdateRoleAction($roleRepository);

        // Act & Assert
        $this->expectException(UpdateException::class);
        $this->expectExceptionMessage('Failed to update role');
        $action->execute($roleId, $dto);
    }
}
