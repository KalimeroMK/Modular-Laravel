<?php

declare(strict_types=1);

namespace Tests\Unit\User;

use App\Modules\Core\Exceptions\UpdateException;
use App\Modules\User\Application\Actions\UpdateUserAction;
use App\Modules\User\Application\DTO\UpdateUserDTO;
use App\Modules\User\Infrastructure\Models\User;
use App\Modules\User\Infrastructure\Repositories\UserRepositoryInterface;
use Illuminate\Support\Facades\Hash;
use Mockery;
use Override;
use Tests\TestCase;

class UpdateUserActionTest extends TestCase
{
    #[Override]
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
        $user->name = 'Original User';
        $user->email = 'original@example.com';

        $updatedUser = new User();
        $updatedUser->id = $userId;
        $updatedUser->name = $name;
        $updatedUser->email = $email;

        $userRepository = Mockery::mock(UserRepositoryInterface::class);
        $userRepository->shouldReceive('findOrFail')->with($userId)->andReturn($user);
        $userRepository->shouldReceive('update')
            ->with($userId, Mockery::on(fn ($data) => $data['name'] === $name
                && $data['email'] === $email
                && isset($data['password'])))
            ->andReturn($updatedUser);

        Hash::shouldReceive('make')
            ->with($password)
            ->andReturn('hashed-password');

        $action = new UpdateUserAction($userRepository);

        // Act
        $result = $action->execute($userId, $dto);

        // Assert
        $this->assertInstanceOf(User::class, $result);
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

        $user = new User();
        $user->id = $userId;

        $userRepository = Mockery::mock(UserRepositoryInterface::class);
        $userRepository->shouldReceive('findOrFail')->with($userId)->andReturn($user);
        $userRepository->shouldReceive('update')
            ->andReturn(null);

        Hash::shouldReceive('make')
            ->with($password)
            ->andReturn('hashed-password');

        $action = new UpdateUserAction($userRepository);

        // Act & Assert
        $this->expectException(UpdateException::class);
        $this->expectExceptionMessage('Failed to update user');
        $action->execute($userId, $dto);
    }
}
