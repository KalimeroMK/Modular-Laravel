<?php

declare(strict_types=1);

namespace Tests\Unit\User;

use App\Modules\User\Application\Actions\DeleteUserAction;
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

        $userId = 1;
        $user = Mockery::mock(\App\Modules\User\Infrastructure\Models\User::class);

        $userRepository = Mockery::mock(UserRepositoryInterface::class);
        $userRepository->shouldReceive('findOrFail')->with($userId)->andReturn($user);
        $userRepository->shouldReceive('delete')
            ->with($userId)
            ->andReturn(true);

        $action = new DeleteUserAction($userRepository);

        $result = $action->execute($userId);

        $this->assertTrue($result);
    }

    public function test_execute_user_deletion_failure(): void
    {

        $userId = 1;
        $user = Mockery::mock(\App\Modules\User\Infrastructure\Models\User::class);

        $userRepository = Mockery::mock(UserRepositoryInterface::class);
        $userRepository->shouldReceive('findOrFail')->with($userId)->andReturn($user);
        $userRepository->shouldReceive('delete')
            ->with($userId)
            ->andReturn(false);

        $action = new DeleteUserAction($userRepository);

        $result = $action->execute($userId);

        $this->assertFalse($result);
    }
}
