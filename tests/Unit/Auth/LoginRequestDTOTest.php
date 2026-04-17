<?php

declare(strict_types=1);

namespace Tests\Unit\Auth;

use App\Modules\Auth\Application\DTO\LoginRequestDTO;
use Tests\TestCase;

class LoginRequestDTOTest extends TestCase
{
    public function test_from_array_creates_dto(): void
    {

        $data = [
            'email' => 'test@example.com',
            'password' => 'password123',
        ];

        $dto = LoginRequestDTO::fromArray($data);

        $this->assertInstanceOf(LoginRequestDTO::class, $dto);
        $this->assertEquals('test@example.com', $dto->email);
        $this->assertEquals('password123', $dto->password);
    }

    public function test_constructor_sets_properties(): void
    {

        $email = 'test@example.com';
        $password = 'password123';

        $dto = new LoginRequestDTO($email, $password);

        $this->assertEquals($email, $dto->email);
        $this->assertEquals($password, $dto->password);
    }

    public function test_to_array_returns_correct_data(): void
    {

        $email = 'test@example.com';
        $password = 'password123';
        $dto = new LoginRequestDTO($email, $password);

        $result = $dto->toArray();

        $this->assertIsArray($result);
        $this->assertEquals($email, $result['email']);
        $this->assertEquals($password, $result['password']);
    }
}
