<?php

declare(strict_types=1);

namespace Tests\Unit\User;

use App\Modules\User\Application\Actions\CreateUserAction;
use App\Modules\User\Application\DTO\CreateUserDTO;
use App\Modules\User\Infrastructure\Models\User;
use App\Modules\User\Infrastructure\Repositories\UserRepositoryInterface;
use Exception;
use Illuminate\Support\Facades\Hash;
use Mockery;
use Override;
use Tests\TestCase;

class CreateUserActionTest extends TestCase
{
    #[Override]
    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }

    public function test_execute_successful_user_creation(): void
    {
        // Arrange
        $name = 'Test User';
        $email = 'test@example.com';
        $password = 'password123';

        $dto = new CreateUserDTO($name, $email, $password);

        $user = new User();
        $user->id = 1;
        $user->name = $name;
        $user->email = $email;

        $userRepository = Mockery::mock(UserRepositoryInterface::class);
        $userRepository->shouldReceive('create')
            ->with(Mockery::on(fn ($data) => $data['name'] === $name
                && $data['email'] === $email
                && isset($data['password'])))
            ->andReturn($user);

        Hash::shouldReceive('make')
            ->with($password)
            ->andReturn('hashed-password');

        $action = new CreateUserAction($userRepository);

        // Act
        $result = $action->execute($dto);

        // Assert
        $this->assertInstanceOf(User::class, $result);
        $this->assertEquals($name, $result->name);
        $this->assertEquals($email, $result->email);
    }

    public function test_execute_user_creation_failure(): void
    {
        // Arrange
        $name = 'Test User';
        $email = 'test@example.com';
        $password = 'password123';

        $dto = new CreateUserDTO($name, $email, $password);

        $userRepository = Mockery::mock(UserRepositoryInterface::class);
        $userRepository->shouldReceive('create')
            ->andReturn(null);

        Hash::shouldReceive('make')
            ->with($password)
            ->andReturn('hashed-password');

        $action = new CreateUserAction($userRepository);

        // Act & Assert
        $this->expectException(Exception::class);
        $this->expectExceptionMessage('Failed to create user');
        $action->execute($dto);
    }
}
