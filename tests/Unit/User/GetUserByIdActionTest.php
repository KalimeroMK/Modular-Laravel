<?php

declare(strict_types=1);

namespace Tests\Unit\User;

use App\Modules\User\Application\Actions\GetUserByIdAction;
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
        $user = new User();
        $user->id = 1;
        $user->name = 'Test User';
        $user->email = 'test@example.com';

        $repository = Mockery::mock(UserRepositoryInterface::class);
        $repository->shouldReceive('findOrFail')
            ->with(1)
            ->once()
            ->andReturn($user);

        $action = new GetUserByIdAction($repository);

        // Act
        $result = $action->execute(1);

        // Assert
        $this->assertInstanceOf(User::class, $result);
        $this->assertEquals(1, $result->id);
        $this->assertEquals('Test User', $result->name);
        $this->assertEquals('test@example.com', $result->email);
    }

    public function test_execute_returns_user_dto(): void
    {
        // Arrange
        $user = new User();
        $user->id = 2;
        $user->name = 'Another User';
        $user->email = 'another@example.com';

        $repository = Mockery::mock(UserRepositoryInterface::class);
        $repository->shouldReceive('findOrFail')
            ->with(2)
            ->once()
            ->andReturn($user);

        $action = new GetUserByIdAction($repository);

        // Act
        $result = $action->execute(2);

        // Assert
        $this->assertInstanceOf(User::class, $result);
        $this->assertEquals(2, $result->id);
        $this->assertEquals('Another User', $result->name);
        $this->assertEquals('another@example.com', $result->email);
    }
}
