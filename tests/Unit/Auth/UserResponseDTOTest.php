<?php

declare(strict_types=1);

namespace Tests\Unit\Auth;

use App\Modules\Auth\Application\DTO\UserResponseDTO;
use App\Modules\User\Infrastructure\Models\User;
use Carbon\Carbon;
use Tests\TestCase;

class UserResponseDTOTest extends TestCase
{
    public function test_constructor_sets_properties(): void
    {
        // Arrange
        $id = 1;
        $name = 'Test User';
        $email = 'test@example.com';
        $emailVerifiedAt = '2023-01-01T00:00:00Z';
        $createdAt = '2023-01-01T00:00:00Z';
        $updatedAt = '2023-01-01T00:00:00Z';

        // Act
        $dto = new UserResponseDTO($id, $name, $email, $emailVerifiedAt, $createdAt, $updatedAt);

        // Assert
        $this->assertEquals($id, $dto->id);
        $this->assertEquals($name, $dto->name);
        $this->assertEquals($email, $dto->email);
        $this->assertEquals($emailVerifiedAt, $dto->emailVerifiedAt);
        $this->assertEquals($createdAt, $dto->createdAt);
        $this->assertEquals($updatedAt, $dto->updatedAt);
    }

    public function test_from_user_creates_dto(): void
    {
        // Arrange
        $user = new User();
        $user->id = 1;
        $user->name = 'Test User';
        $user->email = 'test@example.com';
        $user->email_verified_at = Carbon::parse('2023-01-01T00:00:00Z');
        $user->created_at = Carbon::parse('2023-01-01T00:00:00Z');
        $user->updated_at = Carbon::parse('2023-01-01T00:00:00Z');

        // Act
        $dto = UserResponseDTO::fromUser($user);

        // Assert
        $this->assertInstanceOf(UserResponseDTO::class, $dto);
        $this->assertEquals(1, $dto->id);
        $this->assertEquals('Test User', $dto->name);
        $this->assertEquals('test@example.com', $dto->email);
        $this->assertStringContainsString('2023-01-01T00:00:00', $dto->emailVerifiedAt);
    }

    public function test_to_array_returns_correct_data(): void
    {
        // Arrange
        $dto = new UserResponseDTO(
            1,
            'Test User',
            'test@example.com',
            '2023-01-01T00:00:00Z',
            '2023-01-01T00:00:00Z',
            '2023-01-01T00:00:00Z'
        );

        // Act
        $result = $dto->toArray();

        // Assert
        $this->assertIsArray($result);
        $this->assertEquals(1, $result['id']);
        $this->assertEquals('Test User', $result['name']);
        $this->assertEquals('test@example.com', $result['email']);
        $this->assertEquals('2023-01-01T00:00:00Z', $result['email_verified_at']);
        $this->assertEquals('2023-01-01T00:00:00Z', $result['created_at']);
        $this->assertEquals('2023-01-01T00:00:00Z', $result['updated_at']);
    }
}
