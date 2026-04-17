<?php

declare(strict_types=1);

namespace Tests\Unit\Auth;

use App\Modules\Auth\Application\DTO\RegisterUserDTO;
use Tests\TestCase;

class RegisterUserDTOTest extends TestCase
{
    public function test_from_array_creates_dto(): void
    {

        $data = [
            'name' => 'Test User',
            'email' => 'test@example.com',
            'password' => 'password123',
        ];

        $dto = RegisterUserDTO::fromArray($data);

        $this->assertInstanceOf(RegisterUserDTO::class, $dto);
        $this->assertEquals('Test User', $dto->name);
        $this->assertEquals('test@example.com', $dto->email);
        $this->assertEquals('password123', $dto->password);
    }

    public function test_constructor_sets_properties(): void
    {

        $name = 'Test User';
        $email = 'test@example.com';
        $password = 'password123';

        $dto = new RegisterUserDTO($name, $email, $password);

        $this->assertEquals($name, $dto->name);
        $this->assertEquals($email, $dto->email);
        $this->assertEquals($password, $dto->password);
    }

    public function test_to_array_returns_correct_data(): void
    {

        $name = 'Test User';
        $email = 'test@example.com';
        $password = 'password123';
        $dto = new RegisterUserDTO($name, $email, $password);

        $result = $dto->toArray();

        $this->assertIsArray($result);
        $this->assertEquals($name, $result['name']);
        $this->assertEquals($email, $result['email']);
        $this->assertEquals($password, $result['password']);
    }
}
