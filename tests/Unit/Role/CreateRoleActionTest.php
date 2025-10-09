<?php

declare(strict_types=1);

namespace Tests\Unit\Role;

use App\Modules\Role\Application\Actions\CreateRoleAction;
use App\Modules\Role\Application\DTO\CreateRoleDTO;
use App\Modules\Role\Application\DTO\RoleResponseDTO;
use App\Modules\Role\Infrastructure\Models\Role;
use App\Modules\Role\Infrastructure\Repositories\RoleRepositoryInterface;
use Exception;
use Mockery;
use Tests\TestCase;

class CreateRoleActionTest extends TestCase
{
    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }

    public function test_execute_successful_role_creation(): void
    {
        // Arrange
        $name = 'admin';
        $guardName = 'web';
        
        $dto = new CreateRoleDTO($name, $guardName);
        
        $role = new Role();
        $role->id = 1;
        $role->name = $name;
        $role->guard_name = $guardName;

        $roleRepository = Mockery::mock(RoleRepositoryInterface::class);
        $roleRepository->shouldReceive('create')
            ->with(Mockery::on(function ($data) use ($name, $guardName) {
                return $data['name'] === $name 
                    && $data['guard_name'] === $guardName;
            }))
            ->andReturn($role);

        $action = new CreateRoleAction($roleRepository);

        // Act
        $result = $action->execute($dto);

        // Assert
        $this->assertInstanceOf(RoleResponseDTO::class, $result);
        $this->assertEquals($name, $result->name);
        $this->assertEquals($guardName, $result->guardName);
    }

    public function test_execute_role_creation_failure(): void
    {
        // Arrange
        $name = 'admin';
        $guardName = 'web';
        
        $dto = new CreateRoleDTO($name, $guardName);

        $roleRepository = Mockery::mock(RoleRepositoryInterface::class);
        $roleRepository->shouldReceive('create')
            ->andReturn(null);

        $action = new CreateRoleAction($roleRepository);

        // Act & Assert
        $this->expectException(Exception::class);
        $this->expectExceptionMessage('Failed to create role');
        $action->execute($dto);
    }
}
