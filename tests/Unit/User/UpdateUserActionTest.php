<?php

declare(strict_types=1);

namespace Tests\Unit\User;

use App\Modules\User\Application\Actions\UpdateUserAction;
use App\Modules\User\Application\DTO\UpdateUserDTO;
use App\Modules\User\Application\DTO\UserResponseDTO;
use App\Modules\User\Infrastructure\Models\User;
use App\Modules\User\Infrastructure\Repositories\UserRepositoryInterface;
use Exception;
use Illuminate\Support\Facades\Hash;
use Mockery;
use Tests\TestCase;

class UpdateUserActionTest extends TestCase
{
    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }

    public function test_execute_successful_user_update(): void
    {
        // Arrange
        $userId = 1;
        $name = 'Updated User';
        $email = 'updated@example.com';
        $password = 'newpassword123';
        
        $dto = new UpdateUserDTO($name, $email, $password);
        
        $user = new User();
        $user->id = $userId;
        $user->name = $name;
        $user->email = $email;

        $userRepository = Mockery::mock(UserRepositoryInterface::class);
        $userRepository->shouldReceive('update')
            ->with($userId, Mockery::on(function ($data) use ($name, $email) {
                return $data['name'] === $name 
                    && $data['email'] === $email 
                    && isset($data['password']);
            }))
            ->andReturn($user);

        Hash::shouldReceive('make')
            ->with($password)
            ->andReturn('hashed-password');

        $action = new UpdateUserAction($userRepository);

        // Act
        $result = $action->execute($userId, $dto);

        // Assert
        $this->assertInstanceOf(UserResponseDTO::class, $result);
        $this->assertEquals($name, $result->name);
        $this->assertEquals($email, $result->email);
    }

    public function test_execute_user_update_failure(): void
    {
        // Arrange
        $userId = 1;
        $name = 'Updated User';
        $email = 'updated@example.com';
        $password = 'newpassword123';
        
        $dto = new UpdateUserDTO($name, $email, $password);

        $userRepository = Mockery::mock(UserRepositoryInterface::class);
        $userRepository->shouldReceive('update')
            ->andReturn(null);

        Hash::shouldReceive('make')
            ->with($password)
            ->andReturn('hashed-password');

        $action = new UpdateUserAction($userRepository);

        // Act & Assert
        $this->expectException(Exception::class);
        $this->expectExceptionMessage('Failed to update user');
        $action->execute($userId, $dto);
    }
}
