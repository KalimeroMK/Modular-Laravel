<?php

declare(strict_types=1);

namespace Tests\Unit\Role;

use App\Modules\Role\Application\Actions\GetRoleByIdAction;
use App\Modules\Role\Infrastructure\Models\Role;
use App\Modules\Role\Infrastructure\Repositories\RoleRepositoryInterface;
use Mockery;
use Tests\TestCase;

class GetRoleByIdActionTest extends TestCase
{
    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }

    public function test_execute_returns_role_when_found(): void
    {
        // Arrange
        $role = new Role();
        $role->id = 1;
        $role->name = 'admin';
        $role->guard_name = 'api';

        $repository = Mockery::mock(RoleRepositoryInterface::class);
        $repository->shouldReceive('findOrFail')
            ->with(1)
            ->once()
            ->andReturn($role);

        $action = new GetRoleByIdAction($repository);

        // Act
        $result = $action->execute(1);

        // Assert
        $this->assertInstanceOf(Role::class, $result);
        $this->assertEquals(1, $result->id);
        $this->assertEquals('admin', $result->name);
        $this->assertEquals('api', $result->guard_name);
    }

    public function test_execute_returns_role_dto(): void
    {
        // Arrange
        $role = new Role();
        $role->id = 2;
        $role->name = 'user';
        $role->guard_name = 'api';

        $repository = Mockery::mock(RoleRepositoryInterface::class);
        $repository->shouldReceive('findOrFail')
            ->with(2)
            ->once()
            ->andReturn($role);

        $action = new GetRoleByIdAction($repository);

        // Act
        $result = $action->execute(2);

        // Assert
        $this->assertInstanceOf(Role::class, $result);
        $this->assertEquals(2, $result->id);
        $this->assertEquals('user', $result->name);
    }
}
