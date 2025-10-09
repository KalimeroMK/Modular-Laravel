<?php

declare(strict_types=1);

namespace Tests\Unit\User;

use App\Modules\User\Application\DTO\UpdateUserDTO;
use Tests\TestCase;

class UpdateUserDTOTest extends TestCase
{
    public function test_from_array_creates_dto(): void
    {
        // Arrange
        $data = [
            'name' => 'Updated User',
            'email' => 'updated@example.com',
            'password' => 'newpassword123',
        ];

        // Act
        $dto = UpdateUserDTO::fromArray($data);

        // Assert
        $this->assertInstanceOf(UpdateUserDTO::class, $dto);
        $this->assertEquals('Updated User', $dto->name);
        $this->assertEquals('updated@example.com', $dto->email);
        $this->assertEquals('newpassword123', $dto->password);
    }

    public function test_constructor_sets_properties(): void
    {
        // Arrange
        $name = 'Updated User';
        $email = 'updated@example.com';
        $password = 'newpassword123';

        // Act
        $dto = new UpdateUserDTO($name, $email, $password);

        // Assert
        $this->assertEquals($name, $dto->name);
        $this->assertEquals($email, $dto->email);
        $this->assertEquals($password, $dto->password);
    }

    public function test_to_array_returns_correct_data(): void
    {
        // Arrange
        $name = 'Updated User';
        $email = 'updated@example.com';
        $password = 'newpassword123';
        $dto = new UpdateUserDTO($name, $email, $password);

        // Act
        $result = $dto->toArray();

        // Assert
        $this->assertIsArray($result);
        $this->assertEquals($name, $result['name']);
        $this->assertEquals($email, $result['email']);
        $this->assertEquals($password, $result['password']);
    }

    public function test_constructor_with_null_values(): void
    {
        // Arrange
        $name = 'Updated User';
        $email = 'updated@example.com';

        // Act
        $dto = new UpdateUserDTO($name, $email, null);

        // Assert
        $this->assertEquals($name, $dto->name);
        $this->assertEquals($email, $dto->email);
        $this->assertNull($dto->password);
    }
}
