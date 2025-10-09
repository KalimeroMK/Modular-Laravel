<?php

declare(strict_types=1);

namespace Tests\Unit\User;

use App\Modules\User\Application\Actions\GetUserByIdAction;
use App\Modules\User\Application\DTO\UserResponseDTO;
use App\Modules\User\Infrastructure\Models\User;
use App\Modules\User\Infrastructure\Repositories\UserRepositoryInterface;
use Mockery;
use Tests\TestCase;

class GetUserByIdActionTest extends TestCase
{
    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }

    public function test_execute_returns_user_when_found(): void
    {
        // Arrange
        $userId = 1;
        $user = new User();
        $user->id = $userId;
        $user->name = 'Test User';
        $user->email = 'test@example.com';

        $userRepository = Mockery::mock(UserRepositoryInterface::class);
        $userRepository->shouldReceive('find')
            ->with($userId)
            ->andReturn($user);

        $action = new GetUserByIdAction($userRepository);

        // Act
        $result = $action->execute($userId);

        // Assert
        $this->assertInstanceOf(UserResponseDTO::class, $result);
        $this->assertEquals($userId, $result->id);
        $this->assertEquals('Test User', $result->name);
        $this->assertEquals('test@example.com', $result->email);
    }

    public function test_execute_returns_null_when_user_not_found(): void
    {
        // Arrange
        $userId = 999;

        $userRepository = Mockery::mock(UserRepositoryInterface::class);
        $userRepository->shouldReceive('find')
            ->with($userId)
            ->andReturn(null);

        $action = new GetUserByIdAction($userRepository);

        // Act
        $result = $action->execute($userId);

        // Assert
        $this->assertNull($result);
    }
}
