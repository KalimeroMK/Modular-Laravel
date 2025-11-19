<?php

declare(strict_types=1);

namespace Tests\Unit\Role;

use App\Modules\Role\Application\Actions\UpdateRoleAction;
use App\Modules\Role\Application\DTO\RoleResponseDTO;
use App\Modules\Role\Application\DTO\UpdateRoleDTO;
use App\Modules\Role\Infrastructure\Models\Role;
use App\Modules\Role\Infrastructure\Repositories\RoleRepositoryInterface;
use Exception;
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
        $name = 'updated-admin';
        $guardName = 'api';

        $dto = new UpdateRoleDTO($name, $guardName);

        $role = new Role();
        $role->id = 1;
        $role->name = 'admin';
        $role->guard_name = 'api';

        $updatedRole = new Role();
        $updatedRole->id = 1;
        $updatedRole->name = $name;
        $updatedRole->guard_name = $guardName;

        $roleRepository = Mockery::mock(RoleRepositoryInterface::class);
        $roleRepository->shouldReceive('update')
            ->with(1, Mockery::on(function ($data) use ($name, $guardName) {
                return $data['name'] === $name
                    && $data['guard_name'] === $guardName;
            }))
            ->andReturn($updatedRole);

        $action = new UpdateRoleAction($roleRepository);

        // Act
        $result = $action->execute($role, $dto);

        // Assert
        $this->assertInstanceOf(RoleResponseDTO::class, $result);
        $this->assertEquals($name, $result->name);
        $this->assertEquals($guardName, $result->guardName);
    }

    public function test_execute_role_update_failure(): void
    {
        // Arrange
        $name = 'updated-admin';
        $guardName = 'api';

        $dto = new UpdateRoleDTO($name, $guardName);

        $role = new Role();
        $role->id = 1;

        $roleRepository = Mockery::mock(RoleRepositoryInterface::class);
        $roleRepository->shouldReceive('update')
            ->andReturn(null);

        $action = new UpdateRoleAction($roleRepository);

        // Act & Assert
        $this->expectException(Exception::class);
        $this->expectExceptionMessage('Failed to update role');
        $action->execute($role, $dto);
    }
}
