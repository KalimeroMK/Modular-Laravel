<?php

declare(strict_types=1);

namespace Tests\Unit\Role;

use App\Modules\Role\Application\Actions\GetRoleByIdAction;
use App\Modules\Role\Application\DTO\RoleResponseDTO;
use App\Modules\Role\Infrastructure\Models\Role;
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

        $action = new GetRoleByIdAction();

        // Act
        $result = $action->execute($role);

        // Assert
        $this->assertInstanceOf(RoleResponseDTO::class, $result);
        $this->assertEquals(1, $result->id);
        $this->assertEquals('admin', $result->name);
        $this->assertEquals('api', $result->guardName);
    }

    public function test_execute_returns_role_dto(): void
    {
        // Arrange
        $role = new Role();
        $role->id = 2;
        $role->name = 'user';
        $role->guard_name = 'api';

        $action = new GetRoleByIdAction();

        // Act
        $result = $action->execute($role);

        // Assert
        $this->assertInstanceOf(RoleResponseDTO::class, $result);
        $this->assertEquals(2, $result->id);
        $this->assertEquals('user', $result->name);
    }
}
