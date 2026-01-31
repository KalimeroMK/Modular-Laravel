<?php

declare(strict_types=1);

namespace Tests\Unit\Role;

use App\Modules\Role\Application\Actions\CreateRoleAction;
use App\Modules\Role\Application\DTO\CreateRoleDTO;
use App\Modules\Role\Infrastructure\Models\Role;
use App\Modules\Role\Infrastructure\Repositories\RoleRepositoryInterface;
use Exception;
use Mockery;
use Override;
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
        
        $name = 'admin';
        $guardName = 'api';

        $dto = new CreateRoleDTO($name, $guardName);

        $role = new Role();
        $role->id = 1;
        $role->name = $name;
        $role->guard_name = $guardName;

        $roleRepository = Mockery::mock(RoleRepositoryInterface::class);
        $roleRepository->shouldReceive('create')
            ->with(Mockery::on(fn ($data) => $data['name'] === $name
                && $data['guard_name'] === $guardName))
            ->andReturn($role);

        $action = new CreateRoleAction($roleRepository);

        
        $result = $action->execute($dto);

        
        $this->assertInstanceOf(Role::class, $result);
        $this->assertEquals($name, $result->name);
        $this->assertEquals($guardName, $result->guard_name);
    }

    public function test_execute_role_creation_failure(): void
    {
        
        $name = 'admin';
        $guardName = 'api';

        $dto = new CreateRoleDTO($name, $guardName);

        $roleRepository = Mockery::mock(RoleRepositoryInterface::class);
        $roleRepository->shouldReceive('create')
            ->andReturn(null);

        $action = new CreateRoleAction($roleRepository);

        
        $this->expectException(Exception::class);
        $this->expectExceptionMessage('Failed to create role');
        $action->execute($dto);
    }
}
