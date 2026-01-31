<?php

declare(strict_types=1);

namespace Tests\Unit\Permission;

use App\Modules\Permission\Infrastructure\Models\Permission;
use App\Modules\Permission\Infrastructure\Repositories\PermissionRepository;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Pagination\LengthAwarePaginator;
use Override;
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
        
        $permission = Permission::factory()->create([
            'name' => 'manage-users',
        ]);

        
        $result = $this->repository->findByName('manage-users');

        
        $this->assertInstanceOf(Permission::class, $result);
        $this->assertEquals($permission->id, $result->id);
        $this->assertEquals('manage-users', $result->name);
    }

    public function test_find_by_name_returns_null_when_not_found(): void
    {
        
        $result = $this->repository->findByName('nonexistent');

        
        $this->assertNull($result);
    }

    public function test_paginate_returns_paginated_results(): void
    {
        
        Permission::factory()->count(25)->create();

        
        $result = $this->repository->paginate(10);

        
        $this->assertInstanceOf(LengthAwarePaginator::class, $result);
        $this->assertEquals(10, $result->perPage());
        $this->assertEquals(25, $result->total());
        $this->assertEquals(3, $result->lastPage());
    }

    public function test_create_permission_success(): void
    {
        
        $permissionData = [
            'name' => 'edit-posts',
            'guard_name' => 'api',
        ];

        
        $result = $this->repository->create($permissionData);

        
        $this->assertInstanceOf(Permission::class, $result);
        $this->assertEquals('edit-posts', $result->name);
        $this->assertEquals('api', $result->guard_name);
        $this->assertDatabaseHas('permissions', [
            'name' => 'edit-posts',
            'guard_name' => 'api',
        ]);
    }

    public function test_update_permission_success(): void
    {
        
        $permission = Permission::factory()->create();
        $updateData = [
            'name' => 'updated-manage-users',
        ];

        
        $result = $this->repository->update($permission->id, $updateData);

        
        $this->assertInstanceOf(Permission::class, $result);
        $this->assertEquals('updated-manage-users', $result->name);
        $this->assertDatabaseHas('permissions', [
            'id' => $permission->id,
            'name' => 'updated-manage-users',
        ]);
    }
}
