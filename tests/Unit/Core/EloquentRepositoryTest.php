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
        // Arrange
        User::factory()->count(5)->create();

        // Act
        $result = $this->repository->all();

        // Assert
        $this->assertCount(5, $result);
        $this->assertInstanceOf(\Illuminate\Support\Collection::class, $result);
    }

    public function test_find_returns_user(): void
    {
        // Arrange
        $user = User::factory()->create();

        // Act
        $result = $this->repository->find($user->id);

        // Assert
        $this->assertInstanceOf(User::class, $result);
        $this->assertEquals($user->id, $result->id);
    }

    public function test_find_returns_null_when_not_found(): void
    {
        // Act
        $result = $this->repository->find(999);

        // Assert
        $this->assertNull($result);
    }

    public function test_find_or_fail_returns_user(): void
    {
        // Arrange
        $user = User::factory()->create();

        // Act
        $result = $this->repository->findOrFail($user->id);

        // Assert
        $this->assertInstanceOf(User::class, $result);
        $this->assertEquals($user->id, $result->id);
    }

    public function test_find_or_fail_throws_exception_when_not_found(): void
    {
        // Act & Assert
        $this->expectException(\Illuminate\Database\Eloquent\ModelNotFoundException::class);
        $this->repository->findOrFail(999);
    }

    public function test_find_by_returns_user(): void
    {
        // Arrange
        $user = User::factory()->create([
            'email' => 'test@example.com',
        ]);

        // Act
        $result = $this->repository->findBy('email', 'test@example.com');

        // Assert
        $this->assertInstanceOf(User::class, $result);
        $this->assertEquals($user->id, $result->id);
    }

    public function test_find_by_returns_null_when_not_found(): void
    {
        // Act
        $result = $this->repository->findBy('email', 'nonexistent@example.com');

        // Assert
        $this->assertNull($result);
    }

    public function test_create_returns_user(): void
    {
        // Arrange
        $userData = [
            'name' => 'Test User',
            'email' => 'test@example.com',
            'password' => bcrypt('password123'),
        ];

        // Act
        $result = $this->repository->create($userData);

        // Assert
        $this->assertInstanceOf(User::class, $result);
        $this->assertEquals('Test User', $result->name);
        $this->assertEquals('test@example.com', $result->email);
    }

    public function test_update_returns_updated_user(): void
    {
        // Arrange
        $user = User::factory()->create();
        $updateData = [
            'name' => 'Updated Name',
        ];

        // Act
        $result = $this->repository->update($user->id, $updateData);

        // Assert
        $this->assertInstanceOf(User::class, $result);
        $this->assertEquals('Updated Name', $result->name);
    }

    public function test_delete_returns_true(): void
    {
        // Arrange
        $user = User::factory()->create();

        // Act
        $result = $this->repository->delete($user->id);

        // Assert
        $this->assertTrue($result);
        $this->assertDatabaseMissing('users', [
            'id' => $user->id,
        ]);
    }

    public function test_insert_returns_true(): void
    {
        // Arrange
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

        // Act
        $result = $this->repository->insert($usersData);

        // Assert
        $this->assertTrue($result);
        $this->assertDatabaseHas('users', [
            'email' => 'user1@example.com',
        ]);
        $this->assertDatabaseHas('users', [
            'email' => 'user2@example.com',
        ]);
    }
}
