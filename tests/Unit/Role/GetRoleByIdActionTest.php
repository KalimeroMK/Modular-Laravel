<?php

declare(strict_types=1);

namespace Tests\Unit\Role;

use App\Modules\Role\Application\Actions\GetRoleByIdAction;
use App\Modules\Role\Application\DTO\RoleResponseDTO;
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
        $roleId = 1;
        $role = new Role();
        $role->id = $roleId;
        $role->name = 'admin';
        $role->guard_name = 'web';

        $roleRepository = Mockery::mock(RoleRepositoryInterface::class);
        $roleRepository->shouldReceive('find')
            ->with($roleId)
            ->andReturn($role);

        $action = new GetRoleByIdAction($roleRepository);

        // Act
        $result = $action->execute($roleId);

        // Assert
        $this->assertInstanceOf(RoleResponseDTO::class, $result);
        $this->assertEquals($roleId, $result->id);
        $this->assertEquals('admin', $result->name);
        $this->assertEquals('web', $result->guardName);
    }

    public function test_execute_returns_null_when_role_not_found(): void
    {
        // Arrange
        $roleId = 999;

        $roleRepository = Mockery::mock(RoleRepositoryInterface::class);
        $roleRepository->shouldReceive('find')
            ->with($roleId)
            ->andReturn(null);

        $action = new GetRoleByIdAction($roleRepository);

        // Act
        $result = $action->execute($roleId);

        // Assert
        $this->assertNull($result);
    }
}
