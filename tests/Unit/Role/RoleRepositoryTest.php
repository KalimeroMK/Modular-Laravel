<?php

declare(strict_types=1);

namespace Tests\Unit\Role;

use App\Modules\Role\Infrastructure\Models\Role;
use App\Modules\Role\Infrastructure\Repositories\RoleRepository;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Pagination\LengthAwarePaginator;
use Override;
use Tests\TestCase;

class RoleRepositoryTest extends TestCase
{
    use RefreshDatabase;

    protected RoleRepository $repository;

    
    protected function setUp(): void
    {
        parent::setUp();
        $this->repository = new RoleRepository(new Role());
    }

    public function test_find_by_name_returns_role(): void
    {
        
        $role = Role::factory()->create([
            'name' => 'admin',
        ]);

        
        $result = $this->repository->findByName('admin');

        
        $this->assertInstanceOf(Role::class, $result);
        $this->assertEquals($role->id, $result->id);
        $this->assertEquals('admin', $result->name);
    }

    public function test_find_by_name_returns_null_when_not_found(): void
    {
        
        $result = $this->repository->findByName('nonexistent');

        
        $this->assertNull($result);
    }

    public function test_paginate_returns_paginated_results(): void
    {
        
        Role::factory()->count(25)->create();

        
        $result = $this->repository->paginate(10);

        
        $this->assertInstanceOf(LengthAwarePaginator::class, $result);
        $this->assertEquals(10, $result->perPage());
        $this->assertEquals(25, $result->total());
        $this->assertEquals(3, $result->lastPage());
    }

    public function test_create_role_success(): void
    {
        
        $roleData = [
            'name' => 'editor',
            'guard_name' => 'api',
        ];

        
        $result = $this->repository->create($roleData);

        
        $this->assertInstanceOf(Role::class, $result);
        $this->assertEquals('editor', $result->name);
        $this->assertEquals('api', $result->guard_name);
        $this->assertDatabaseHas('roles', [
            'name' => 'editor',
            'guard_name' => 'api',
        ]);
    }

    public function test_update_role_success(): void
    {
        
        $role = Role::factory()->create();
        $updateData = [
            'name' => 'updated-admin',
        ];

        
        $result = $this->repository->update($role->id, $updateData);

        
        $this->assertInstanceOf(Role::class, $result);
        $this->assertEquals('updated-admin', $result->name);
        $this->assertDatabaseHas('roles', [
            'id' => $role->id,
            'name' => 'updated-admin',
        ]);
    }
}
