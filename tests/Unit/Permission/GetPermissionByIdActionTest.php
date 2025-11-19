<?php

declare(strict_types=1);

namespace Tests\Unit\Permission;

use App\Modules\Permission\Application\Actions\GetPermissionByIdAction;
use App\Modules\Permission\Application\DTO\PermissionResponseDTO;
use App\Modules\Permission\Infrastructure\Models\Permission;
use Mockery;
use Tests\TestCase;

class GetPermissionByIdActionTest extends TestCase
{
    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }

    public function test_execute_returns_permission_when_found(): void
    {
        // Arrange
        $permission = new Permission();
        $permission->id = 1;
        $permission->name = 'manage-users';
        $permission->guard_name = 'api';

        $action = new GetPermissionByIdAction();

        // Act
        $result = $action->execute($permission);

        // Assert
        $this->assertInstanceOf(PermissionResponseDTO::class, $result);
        $this->assertEquals(1, $result->id);
        $this->assertEquals('manage-users', $result->name);
        $this->assertEquals('api', $result->guardName);
    }

    public function test_execute_returns_permission_dto(): void
    {
        // Arrange
        $permission = new Permission();
        $permission->id = 2;
        $permission->name = 'view-users';
        $permission->guard_name = 'api';

        $action = new GetPermissionByIdAction();

        // Act
        $result = $action->execute($permission);

        // Assert
        $this->assertInstanceOf(PermissionResponseDTO::class, $result);
        $this->assertEquals(2, $result->id);
        $this->assertEquals('view-users', $result->name);
    }
}
