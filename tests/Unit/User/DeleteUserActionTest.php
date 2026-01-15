<?php

declare(strict_types=1);

namespace Tests\Unit\User;

use App\Modules\User\Application\Actions\DeleteUserAction;
use App\Modules\User\Infrastructure\Repositories\UserRepositoryInterface;
use Mockery;
use Override;
use Tests\TestCase;

class DeleteUserActionTest extends TestCase
{
    #[Override]
    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }

    public function test_execute_successful_user_deletion(): void
    {
        // Arrange
        $userId = 1;
        $user = Mockery::mock(\App\Modules\User\Infrastructure\Models\User::class);

        $userRepository = Mockery::mock(UserRepositoryInterface::class);
        $userRepository->shouldReceive('findOrFail')->with($userId)->andReturn($user);
        $userRepository->shouldReceive('delete')
            ->with($userId)
            ->andReturn(true);

        $action = new DeleteUserAction($userRepository);

        // Act
        $result = $action->execute($userId);

        // Assert
        $this->assertTrue($result);
    }

    public function test_execute_user_deletion_failure(): void
    {
        // Arrange
        $userId = 1;
        $user = Mockery::mock(\App\Modules\User\Infrastructure\Models\User::class);

        $userRepository = Mockery::mock(UserRepositoryInterface::class);
        $userRepository->shouldReceive('findOrFail')->with($userId)->andReturn($user);
        $userRepository->shouldReceive('delete')
            ->with($userId)
            ->andReturn(false);

        $action = new DeleteUserAction($userRepository);

        // Act
        $result = $action->execute($userId);

        // Assert
        $this->assertFalse($result);
    }
}
