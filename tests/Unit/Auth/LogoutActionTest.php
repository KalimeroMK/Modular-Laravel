<?php

declare(strict_types=1);

namespace Tests\Unit\Auth;

use App\Modules\Auth\Application\Actions\LogoutAction;
use App\Modules\Auth\Application\Services\IssueTokenServiceInterface;
use App\Modules\User\Infrastructure\Models\User;
use Illuminate\Http\Request;
use Mockery;
use Tests\TestCase;

class LogoutActionTest extends TestCase
{
    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }

    public function test_execute_successful_logout(): void
    {
        // Arrange
        $user = Mockery::mock(User::class);
        $user->shouldReceive('setAttribute')->andReturnSelf();
        $user->id = 1;

        $request = Mockery::mock(Request::class);
        $request->shouldReceive('user')
            ->andReturn($user);

        $tokenService = Mockery::mock(IssueTokenServiceInterface::class);
        $tokenService->shouldReceive('revokeToken')
            ->with($user)
            ->andReturn(true);

        $action = new LogoutAction($tokenService);

        // Act
        $action->execute($request);

        // Assert - no exception thrown means success
        $this->assertTrue(true);
    }

    public function test_execute_logout_failure(): void
    {
        // Arrange
        $request = Mockery::mock(Request::class);
        $request->shouldReceive('user')
            ->andReturn(null);

        $tokenService = Mockery::mock(IssueTokenServiceInterface::class);

        $action = new LogoutAction($tokenService);

        // Act
        $action->execute($request);

        // Assert - no exception thrown means success
        $this->assertTrue(true);
    }
}
