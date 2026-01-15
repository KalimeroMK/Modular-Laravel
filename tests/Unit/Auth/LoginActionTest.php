<?php

declare(strict_types=1);

namespace Tests\Unit\Auth;

use App\Modules\Auth\Application\Actions\LoginAction;
use App\Modules\Auth\Application\DTO\LoginRequestDTO;
use App\Modules\Auth\Application\Services\IssueTokenServiceInterface;
use App\Modules\Auth\Infrastructure\Repositories\AuthRepositoryInterface;
use App\Modules\User\Infrastructure\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;
use Mockery;
use Override;
use Tests\TestCase;

class LoginActionTest extends TestCase
{
    #[Override]
    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }

    public function test_execute_successful_login(): void
    {
        // Arrange
        $email = 'test@example.com';
        $password = 'password123';
        $user = new User();
        $user->id = 1;
        $user->name = 'Test User';
        $user->email = $email;

        $dto = new LoginRequestDTO($email, $password);

        $authRepository = Mockery::mock(AuthRepositoryInterface::class);
        $authRepository->shouldReceive('findByEmail')
            ->with($email)
            ->andReturn($user);

        $tokenService = Mockery::mock(IssueTokenServiceInterface::class);
        $tokenService->shouldReceive('issueToken')
            ->with($user)
            ->andReturn(new \App\Modules\Auth\Application\DTO\SessionTokenDTO('test-token', 'Bearer', 3600));

        Auth::shouldReceive('attempt')
            ->with(['email' => $email, 'password' => $password])
            ->andReturn(true);

        $action = new LoginAction($authRepository, $tokenService);

        // Act
        $result = $action->execute($dto);

        // Assert
        $this->assertIsArray($result);
        $this->assertArrayHasKey('user', $result);
        $this->assertArrayHasKey('token', $result);
        $this->assertEquals('test-token', $result['token']);
    }

    public function test_execute_failed_login(): void
    {
        // Arrange
        $email = 'test@example.com';
        $password = 'wrongpassword';
        $dto = new LoginRequestDTO($email, $password);

        $authRepository = Mockery::mock(AuthRepositoryInterface::class);
        $tokenService = Mockery::mock(IssueTokenServiceInterface::class);

        Auth::shouldReceive('attempt')
            ->with(['email' => $email, 'password' => $password])
            ->andReturn(false);

        $action = new LoginAction($authRepository, $tokenService);

        // Act & Assert
        $this->expectException(ValidationException::class);
        $action->execute($dto);
    }

    public function test_execute_user_not_found(): void
    {
        // Arrange
        $email = 'test@example.com';
        $password = 'password123';
        $dto = new LoginRequestDTO($email, $password);

        $authRepository = Mockery::mock(AuthRepositoryInterface::class);
        $authRepository->shouldReceive('findByEmail')
            ->with($email)
            ->andReturn(null);

        $tokenService = Mockery::mock(IssueTokenServiceInterface::class);

        Auth::shouldReceive('attempt')
            ->with(['email' => $email, 'password' => $password])
            ->andReturn(true);

        $action = new LoginAction($authRepository, $tokenService);

        // Act & Assert
        $this->expectException(ValidationException::class);
        $action->execute($dto);
    }
}
