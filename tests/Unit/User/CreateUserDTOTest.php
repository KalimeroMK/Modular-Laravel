<?php

declare(strict_types=1);

namespace Tests\Unit\User;

use App\Modules\User\Application\DTO\CreateUserDTO;
use Tests\TestCase;

class CreateUserDTOTest extends TestCase
{
    public function test_from_array_creates_dto(): void
    {

        $data = [
            'name' => 'Test User',
            'email' => 'test@example.com',
            'password' => 'password123',
            'email_verified_at' => '2023-01-01T00:00:00Z',
        ];

        $dto = CreateUserDTO::fromArray($data);

        $this->assertInstanceOf(CreateUserDTO::class, $dto);
        $this->assertEquals('Test User', $dto->name);
        $this->assertEquals('test@example.com', $dto->email);
        $this->assertEquals('password123', $dto->password);
        $this->assertEquals('2023-01-01T00:00:00Z', $dto->emailVerifiedAt);
    }

    public function test_constructor_sets_properties(): void
    {

        $name = 'Test User';
        $email = 'test@example.com';
        $password = 'password123';
        $emailVerifiedAt = '2023-01-01T00:00:00Z';

        $dto = new CreateUserDTO($name, $email, $password, $emailVerifiedAt);

        $this->assertEquals($name, $dto->name);
        $this->assertEquals($email, $dto->email);
        $this->assertEquals($password, $dto->password);
        $this->assertEquals($emailVerifiedAt, $dto->emailVerifiedAt);
    }

    public function test_to_array_returns_correct_data(): void
    {

        $name = 'Test User';
        $email = 'test@example.com';
        $password = 'password123';
        $emailVerifiedAt = '2023-01-01T00:00:00Z';
        $dto = new CreateUserDTO($name, $email, $password, $emailVerifiedAt);

        $result = $dto->toArray();

        $this->assertIsArray($result);
        $this->assertEquals($name, $result['name']);
        $this->assertEquals($email, $result['email']);
        $this->assertEquals($password, $result['password']);
        $this->assertEquals($emailVerifiedAt, $result['email_verified_at']);
    }
}
