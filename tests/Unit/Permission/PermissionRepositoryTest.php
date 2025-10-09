<?php

declare(strict_types=1);

namespace Tests\Unit\Permission;

use App\Modules\Permission\Infrastructure\Repositories\PermissionRepository;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Pagination\LengthAwarePaginator;
use App\Modules\Permission\Infrastructure\Models\Permission;
use Tests\TestCase;

class PermissionRepositoryTest extends TestCase
{
    use RefreshDatabase;

    protected PermissionRepository $repository;

    protected function setUp(): void
    {
        parent::setUp();
        $this->repository = new PermissionRepository(new Permission());
    }

    public function test_find_by_name_returns_permission(): void
    {
        // Arrange
        $permission = Permission::factory()->create([
            'name' => 'manage-users',
        ]);

        // Act
        $result = $this->repository->findByName('manage-users');

        // Assert
        $this->assertInstanceOf(Permission::class, $result);
        $this->assertEquals($permission->id, $result->id);
        $this->assertEquals('manage-users', $result->name);
    }

    public function test_find_by_name_returns_null_when_not_found(): void
    {
        // Act
        $result = $this->repository->findByName('nonexistent');

        // Assert
        $this->assertNull($result);
    }

    public function test_paginate_returns_paginated_results(): void
    {
        // Arrange
        Permission::factory()->count(25)->create();

        // Act
        $result = $this->repository->paginate(10);

        // Assert
        $this->assertInstanceOf(LengthAwarePaginator::class, $result);
        $this->assertEquals(10, $result->perPage());
        $this->assertEquals(25, $result->total());
        $this->assertEquals(3, $result->lastPage());
    }

    public function test_create_permission_success(): void
    {
        // Arrange
        $permissionData = [
            'name' => 'edit-posts',
            'guard_name' => 'web',
        ];

        // Act
        $result = $this->repository->create($permissionData);

        // Assert
        $this->assertInstanceOf(Permission::class, $result);
        $this->assertEquals('edit-posts', $result->name);
        $this->assertEquals('web', $result->guard_name);
        $this->assertDatabaseHas('permissions', [
            'name' => 'edit-posts',
            'guard_name' => 'web',
        ]);
    }

    public function test_update_permission_success(): void
    {
        // Arrange
        $permission = Permission::factory()->create();
        $updateData = [
            'name' => 'updated-manage-users',
        ];

        // Act
        $result = $this->repository->update($permission->id, $updateData);

        // Assert
        $this->assertInstanceOf(Permission::class, $result);
        $this->assertEquals('updated-manage-users', $result->name);
        $this->assertDatabaseHas('permissions', [
            'id' => $permission->id,
            'name' => 'updated-manage-users',
        ]);
    }
}
