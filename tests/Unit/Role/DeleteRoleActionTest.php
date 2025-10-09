<?php

declare(strict_types=1);

namespace Tests\Unit\Role;

use App\Modules\Role\Application\Actions\DeleteRoleAction;
use App\Modules\Role\Infrastructure\Models\Role;
use App\Modules\Role\Infrastructure\Repositories\RoleRepositoryInterface;
use Mockery;
use Tests\TestCase;

class DeleteRoleActionTest extends TestCase
{
    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }

    public function test_execute_successful_role_deletion(): void
    {
        // Arrange
        $role = new Role();
        $role->id = 1;

        $roleRepository = Mockery::mock(RoleRepositoryInterface::class);
        $roleRepository->shouldReceive('delete')
            ->with(1)
            ->andReturn(true);

        $action = new DeleteRoleAction($roleRepository);

        // Act
        $result = $action->execute($role);

        // Assert
        $this->assertTrue($result);
    }

    public function test_execute_role_deletion_failure(): void
    {
        // Arrange
        $role = new Role();
        $role->id = 1;

        $roleRepository = Mockery::mock(RoleRepositoryInterface::class);
        $roleRepository->shouldReceive('delete')
            ->with(1)
            ->andReturn(false);

        $action = new DeleteRoleAction($roleRepository);

        // Act
        $result = $action->execute($role);

        // Assert
        $this->assertFalse($result);
    }
}
