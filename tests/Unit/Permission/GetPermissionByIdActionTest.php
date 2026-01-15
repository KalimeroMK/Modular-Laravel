<?php

declare(strict_types=1);

namespace Tests\Unit\Permission;

use App\Modules\Permission\Application\Actions\GetPermissionByIdAction;
use App\Modules\Permission\Infrastructure\Models\Permission;
use App\Modules\Permission\Infrastructure\Repositories\PermissionRepositoryInterface;
use Mockery;
use Override;
use Tests\TestCase;

class GetPermissionByIdActionTest extends TestCase
{
    #[Override]
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

        $repository = Mockery::mock(PermissionRepositoryInterface::class);
        $repository->shouldReceive('findOrFail')
            ->with(1)
            ->once()
            ->andReturn($permission);

        $action = new GetPermissionByIdAction($repository);

        // Act
        $result = $action->execute(1);

        // Assert
        $this->assertInstanceOf(Permission::class, $result);
        $this->assertEquals(1, $result->id);
        $this->assertEquals('manage-users', $result->name);
        $this->assertEquals('api', $result->guard_name);
    }

    public function test_execute_returns_permission_dto(): void
    {
        // Arrange
        $permission = new Permission();
        $permission->id = 2;
        $permission->name = 'view-users';
        $permission->guard_name = 'api';

        $repository = Mockery::mock(PermissionRepositoryInterface::class);
        $repository->shouldReceive('findOrFail')
            ->with(2)
            ->once()
            ->andReturn($permission);

        $action = new GetPermissionByIdAction($repository);

        // Act
        $result = $action->execute(2);

        // Assert
        $this->assertInstanceOf(Permission::class, $result);
        $this->assertEquals(2, $result->id);
        $this->assertEquals('view-users', $result->name);
    }
}
