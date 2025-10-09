<?php

declare(strict_types=1);

namespace Tests\Unit\User;

use App\Modules\User\Application\DTO\CreateUserDTO;
use Tests\TestCase;

class CreateUserDTOTest extends TestCase
{
    public function test_from_array_creates_dto(): void
    {
        // Arrange
        $data = [
            'name' => 'Test User',
            'email' => 'test@example.com',
            'password' => 'password123',
            'email_verified_at' => '2023-01-01T00:00:00Z',
        ];

        // Act
        $dto = CreateUserDTO::fromArray($data);

        // Assert
        $this->assertInstanceOf(CreateUserDTO::class, $dto);
        $this->assertEquals('Test User', $dto->name);
        $this->assertEquals('test@example.com', $dto->email);
        $this->assertEquals('password123', $dto->password);
        $this->assertEquals('2023-01-01T00:00:00Z', $dto->emailVerifiedAt);
    }

    public function test_constructor_sets_properties(): void
    {
        // Arrange
        $name = 'Test User';
        $email = 'test@example.com';
        $password = 'password123';
        $emailVerifiedAt = '2023-01-01T00:00:00Z';

        // Act
        $dto = new CreateUserDTO($name, $email, $password, $emailVerifiedAt);

        // Assert
        $this->assertEquals($name, $dto->name);
        $this->assertEquals($email, $dto->email);
        $this->assertEquals($password, $dto->password);
        $this->assertEquals($emailVerifiedAt, $dto->emailVerifiedAt);
    }

    public function test_to_array_returns_correct_data(): void
    {
        // Arrange
        $name = 'Test User';
        $email = 'test@example.com';
        $password = 'password123';
        $emailVerifiedAt = '2023-01-01T00:00:00Z';
        $dto = new CreateUserDTO($name, $email, $password, $emailVerifiedAt);

        // Act
        $result = $dto->toArray();

        // Assert
        $this->assertIsArray($result);
        $this->assertEquals($name, $result['name']);
        $this->assertEquals($email, $result['email']);
        $this->assertEquals($password, $result['password']);
        $this->assertEquals($emailVerifiedAt, $result['email_verified_at']);
    }
}
