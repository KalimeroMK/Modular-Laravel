<?php

declare(strict_types=1);

namespace Tests\Unit\User;

use App\Modules\User\Application\Actions\DeleteUserAction;
use App\Modules\User\Infrastructure\Models\User;
use App\Modules\User\Infrastructure\Repositories\UserRepositoryInterface;
use Mockery;
use Tests\TestCase;

class DeleteUserActionTest extends TestCase
{
    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }

    public function test_execute_successful_user_deletion(): void
    {
        // Arrange
        $user = new User();
        $user->id = 1;

        $userRepository = Mockery::mock(UserRepositoryInterface::class);
        $userRepository->shouldReceive('delete')
            ->with(1)
            ->andReturn(true);

        $action = new DeleteUserAction($userRepository);

        // Act
        $result = $action->execute($user);

        // Assert
        $this->assertTrue($result);
    }

    public function test_execute_user_deletion_failure(): void
    {
        // Arrange
        $user = new User();
        $user->id = 1;

        $userRepository = Mockery::mock(UserRepositoryInterface::class);
        $userRepository->shouldReceive('delete')
            ->with(1)
            ->andReturn(false);

        $action = new DeleteUserAction($userRepository);

        // Act
        $result = $action->execute($user);

        // Assert
        $this->assertFalse($result);
    }
}
