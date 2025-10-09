<?php

declare(strict_types=1);

namespace Tests\Unit\Auth;

use App\Modules\Auth\Application\Actions\RegisterUserAction;
use App\Modules\Auth\Application\DTO\RegisterUserDTO;
use App\Modules\Auth\Application\Services\IssueTokenServiceInterface;
use App\Modules\Auth\Infrastructure\Repositories\AuthRepositoryInterface;
use App\Modules\User\Infrastructure\Models\User;
use Exception;
use Illuminate\Support\Facades\Hash;
use Mockery;
use Tests\TestCase;

class RegisterUserActionTest extends TestCase
{
    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }

    public function test_execute_successful_registration(): void
    {
        // Arrange
        $name = 'Test User';
        $email = 'test@example.com';
        $password = 'password123';
        
        $dto = new RegisterUserDTO($name, $email, $password);
        
        $user = new User();
        $user->id = 1;
        $user->name = $name;
        $user->email = $email;

        $authRepository = Mockery::mock(AuthRepositoryInterface::class);
        $authRepository->shouldReceive('create')
            ->with(Mockery::on(function ($data) use ($name, $email) {
                return $data['name'] === $name 
                    && $data['email'] === $email 
                    && isset($data['password']);
            }))
            ->andReturn($user);

        $tokenService = Mockery::mock(IssueTokenServiceInterface::class);
        $tokenService->shouldReceive('issueToken')
            ->with($user)
            ->andReturn(new \App\Modules\Auth\Application\DTO\SessionTokenDTO('test-token', 'Bearer', 3600));

        Hash::shouldReceive('make')
            ->with($password)
            ->andReturn('hashed-password');

        $action = new RegisterUserAction($authRepository, $tokenService);

        // Act
        $result = $action->execute($dto);

        // Assert
        $this->assertIsArray($result);
        $this->assertArrayHasKey('user', $result);
        $this->assertArrayHasKey('token', $result);
        $this->assertEquals('test-token', $result['token']);
    }

    public function test_execute_registration_failure(): void
    {
        // Arrange
        $name = 'Test User';
        $email = 'test@example.com';
        $password = 'password123';
        
        $dto = new RegisterUserDTO($name, $email, $password);

        $authRepository = Mockery::mock(AuthRepositoryInterface::class);
        $authRepository->shouldReceive('create')
            ->andReturn(null);

        $tokenService = Mockery::mock(IssueTokenServiceInterface::class);

        Hash::shouldReceive('make')
            ->with($password)
            ->andReturn('hashed-password');

        $action = new RegisterUserAction($authRepository, $tokenService);

        // Act & Assert
        $this->expectException(Exception::class);
        $this->expectExceptionMessage('Failed to create user');
        $action->execute($dto);
    }
}
