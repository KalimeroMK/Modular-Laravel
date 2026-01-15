<?php

declare(strict_types=1);

namespace Tests\Unit\Auth;

use App\Modules\Auth\Infrastructure\Repositories\AuthRepository;
use App\Modules\User\Infrastructure\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Override;
use Tests\TestCase;

class AuthRepositoryTest extends TestCase
{
    use RefreshDatabase;

    protected AuthRepository $repository;

    #[Override]
    protected function setUp(): void
    {
        parent::setUp();
        $this->repository = new AuthRepository(new User());
    }

    public function test_find_by_email_returns_user(): void
    {
        // Arrange
        $user = User::factory()->create([
            'email' => 'test@example.com',
        ]);

        // Act
        $result = $this->repository->findByEmail('test@example.com');

        // Assert
        $this->assertInstanceOf(User::class, $result);
        $this->assertEquals($user->id, $result->id);
        $this->assertEquals('test@example.com', $result->email);
    }

    public function test_find_by_email_returns_null_when_not_found(): void
    {
        // Act
        $result = $this->repository->findByEmail('nonexistent@example.com');

        // Assert
        $this->assertNull($result);
    }

    public function test_create_user_success(): void
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
        $this->assertDatabaseHas('users', [
            'name' => 'Test User',
            'email' => 'test@example.com',
        ]);
    }

    public function test_update_user_success(): void
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
        $this->assertDatabaseHas('users', [
            'id' => $user->id,
            'name' => 'Updated Name',
        ]);
    }
}
