<?php

declare(strict_types=1);

namespace Tests\Unit\Role;

use App\Modules\Role\Application\Actions\DeleteRoleAction;
use App\Modules\Role\Infrastructure\Repositories\RoleRepositoryInterface;
use Illuminate\Support\Facades\DB;
use Mockery;
use Override;
use Tests\TestCase;

class DeleteRoleActionTest extends TestCase
{
    #[Override]
    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }

    public function test_execute_successful_role_deletion(): void
    {
        // Arrange
        $roleId = 1;
        $role = Mockery::mock(\App\Modules\Role\Infrastructure\Models\Role::class);

        $roleRepository = Mockery::mock(RoleRepositoryInterface::class);
        $roleRepository->shouldReceive('findOrFail')->with($roleId)->andReturn($role);
        $roleRepository->shouldReceive('delete')->with($roleId)->andReturn(true);

        // Mock DB query builder chain
        $queryBuilder = Mockery::mock();
        $queryBuilder->shouldReceive('where')->with('role_id', $roleId)->andReturnSelf();
        $queryBuilder->shouldReceive('delete')->andReturn(1);

        $queryBuilder2 = Mockery::mock();
        $queryBuilder2->shouldReceive('where')->with('role_id', $roleId)->andReturnSelf();
        $queryBuilder2->shouldReceive('delete')->andReturn(1);

        DB::shouldReceive('table')->with('role_has_permissions')->andReturn($queryBuilder);
        DB::shouldReceive('table')->with('model_has_roles')->andReturn($queryBuilder2);

        $action = new DeleteRoleAction($roleRepository);

        // Act
        $result = $action->execute($roleId);

        // Assert
        $this->assertTrue($result);
    }

    public function test_execute_role_deletion_failure(): void
    {
        // Arrange
        $roleId = 1;
        $role = Mockery::mock(\App\Modules\Role\Infrastructure\Models\Role::class);

        $roleRepository = Mockery::mock(RoleRepositoryInterface::class);
        $roleRepository->shouldReceive('findOrFail')->with($roleId)->andReturn($role);
        $roleRepository->shouldReceive('delete')->with($roleId)->andReturn(false);

        // Mock DB query builder chain
        $queryBuilder = Mockery::mock();
        $queryBuilder->shouldReceive('where')->with('role_id', $roleId)->andReturnSelf();
        $queryBuilder->shouldReceive('delete')->andReturn(1);

        $queryBuilder2 = Mockery::mock();
        $queryBuilder2->shouldReceive('where')->with('role_id', $roleId)->andReturnSelf();
        $queryBuilder2->shouldReceive('delete')->andReturn(1);

        DB::shouldReceive('table')->with('role_has_permissions')->andReturn($queryBuilder);
        DB::shouldReceive('table')->with('model_has_roles')->andReturn($queryBuilder2);

        $action = new DeleteRoleAction($roleRepository);

        // Act
        $result = $action->execute($roleId);

        // Assert
        $this->assertFalse($result);
    }
}
