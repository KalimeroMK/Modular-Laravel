<?php

declare(strict_types=1);

namespace Tests\Unit\Core;

use App\Modules\User\Infrastructure\Models\User;
use App\Modules\User\Infrastructure\Repositories\UserRepository;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class EloquentRepositoryTest extends TestCase
{
    use RefreshDatabase;

    protected UserRepository $repository;

    protected function setUp(): void
    {
        parent::setUp();
        $this->repository = new UserRepository(new User());
    }

    public function test_all_returns_collection(): void
    {

        User::factory()->count(5)->create();

        $result = $this->repository->all();

        $this->assertCount(5, $result);
        $this->assertInstanceOf(\Illuminate\Support\Collection::class, $result);
    }

    public function test_find_returns_user(): void
    {

        $user = User::factory()->create();

        $result = $this->repository->find($user->id);

        $this->assertInstanceOf(User::class, $result);
        $this->assertEquals($user->id, $result->id);
    }

    public function test_find_returns_null_when_not_found(): void
    {

        $result = $this->repository->find(999);

        $this->assertNull($result);
    }

    public function test_find_or_fail_returns_user(): void
    {

        $user = User::factory()->create();

        $result = $this->repository->findOrFail($user->id);

        $this->assertInstanceOf(User::class, $result);
        $this->assertEquals($user->id, $result->id);
    }

    public function test_find_or_fail_throws_exception_when_not_found(): void
    {

        $this->expectException(\Illuminate\Database\Eloquent\ModelNotFoundException::class);
        $this->repository->findOrFail(999);
    }

    public function test_find_by_returns_user(): void
    {

        $user = User::factory()->create([
            'email' => 'test@example.com',
        ]);

        $result = $this->repository->findBy('email', 'test@example.com');

        $this->assertInstanceOf(User::class, $result);
        $this->assertEquals($user->id, $result->id);
    }

    public function test_find_by_returns_null_when_not_found(): void
    {

        $result = $this->repository->findBy('email', 'nonexistent@example.com');

        $this->assertNull($result);
    }

    public function test_create_returns_user(): void
    {

        $userData = [
            'name' => 'Test User',
            'email' => 'test@example.com',
            'password' => bcrypt('password123'),
        ];

        $result = $this->repository->create($userData);

        $this->assertInstanceOf(User::class, $result);
        $this->assertEquals('Test User', $result->name);
        $this->assertEquals('test@example.com', $result->email);
    }

    public function test_update_returns_updated_user(): void
    {

        $user = User::factory()->create();
        $updateData = [
            'name' => 'Updated Name',
        ];

        $result = $this->repository->update($user->id, $updateData);

        $this->assertInstanceOf(User::class, $result);
        $this->assertEquals('Updated Name', $result->name);
    }

    public function test_delete_returns_true(): void
    {

        $user = User::factory()->create();

        $result = $this->repository->delete($user->id);

        $this->assertTrue($result);
        $this->assertDatabaseMissing('users', [
            'id' => $user->id,
        ]);
    }

    public function test_insert_returns_true(): void
    {

        $usersData = [
            [
                'name' => 'User 1',
                'email' => 'user1@example.com',
                'password' => bcrypt('password123'),
            ],
            [
                'name' => 'User 2',
                'email' => 'user2@example.com',
                'password' => bcrypt('password123'),
            ],
        ];

        $result = $this->repository->insert($usersData);

        $this->assertTrue($result);
        $this->assertDatabaseHas('users', [
            'email' => 'user1@example.com',
        ]);
        $this->assertDatabaseHas('users', [
            'email' => 'user2@example.com',
        ]);
    }
}
