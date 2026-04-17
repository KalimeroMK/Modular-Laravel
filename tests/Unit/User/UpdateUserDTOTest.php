<?php

declare(strict_types=1);

namespace Tests\Unit\User;

use App\Modules\User\Application\DTO\UpdateUserDTO;
use Tests\TestCase;

class UpdateUserDTOTest extends TestCase
{
    public function test_from_array_creates_dto(): void
    {

        $data = [
            'name' => 'Updated User',
            'email' => 'updated@example.com',
            'password' => 'newpassword123',
        ];

        $dto = UpdateUserDTO::fromArray($data);

        $this->assertInstanceOf(UpdateUserDTO::class, $dto);
        $this->assertEquals('Updated User', $dto->name);
        $this->assertEquals('updated@example.com', $dto->email);
        $this->assertEquals('newpassword123', $dto->password);
    }

    public function test_constructor_sets_properties(): void
    {

        $name = 'Updated User';
        $email = 'updated@example.com';
        $password = 'newpassword123';

        $dto = new UpdateUserDTO($name, $email, $password);

        $this->assertEquals($name, $dto->name);
        $this->assertEquals($email, $dto->email);
        $this->assertEquals($password, $dto->password);
    }

    public function test_to_array_returns_correct_data(): void
    {

        $name = 'Updated User';
        $email = 'updated@example.com';
        $password = 'newpassword123';
        $dto = new UpdateUserDTO($name, $email, $password);

        $result = $dto->toArray();

        $this->assertIsArray($result);
        $this->assertEquals($name, $result['name']);
        $this->assertEquals($email, $result['email']);
        $this->assertEquals($password, $result['password']);
    }

    public function test_constructor_with_null_values(): void
    {

        $name = 'Updated User';
        $email = 'updated@example.com';

        $dto = new UpdateUserDTO($name, $email);

        $this->assertEquals($name, $dto->name);
        $this->assertEquals($email, $dto->email);
        $this->assertNull($dto->password);
    }
}
