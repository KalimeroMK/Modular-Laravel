<?php

declare(strict_types=1);

namespace Tests\Unit\Auth;

use App\Modules\Auth\Application\Actions\GetCurrentUserAction;
use App\Modules\Auth\Application\DTO\UserResponseDTO;
use App\Modules\Auth\Application\Services\IssueTokenServiceInterface;
use App\Modules\User\Infrastructure\Models\User;
use Exception;
use Illuminate\Http\Request;
use Mockery;
use Tests\TestCase;

class GetCurrentUserActionTest extends TestCase
{
    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }

    public function test_execute_returns_current_user(): void
    {
        // Arrange
        $user = Mockery::mock(User::class);
        $user->shouldReceive('setAttribute')->andReturnSelf();
        $user->shouldReceive('getAttribute')->andReturnUsing(function ($key) {
            return match ($key) {
                'id' => 1,
                'name' => 'Test User',
                'email' => 'test@example.com',
                'email_verified_at' => '2023-01-01T00:00:00.000000Z',
                'created_at' => '2023-01-01T00:00:00.000000Z',
                'updated_at' => '2023-01-01T00:00:00.000000Z',
                default => null
            };
        });
        $user->id = 1;
        $user->name = 'Test User';
        $user->email = 'test@example.com';

        $request = Mockery::mock(Request::class);
        $request->shouldReceive('user')
            ->andReturn($user);

        $tokenService = Mockery::mock(IssueTokenServiceInterface::class);

        $action = new GetCurrentUserAction($tokenService);

        // Act
        $result = $action->execute($request);

        // Assert
        $this->assertInstanceOf(UserResponseDTO::class, $result);
        $this->assertEquals('Test User', $result->name);
        $this->assertEquals('test@example.com', $result->email);
    }

    public function test_execute_throws_exception_when_no_user(): void
    {
        // Arrange
        $request = Mockery::mock(Request::class);
        $request->shouldReceive('user')
            ->andReturn(null);

        $tokenService = Mockery::mock(IssueTokenServiceInterface::class);

        $action = new GetCurrentUserAction($tokenService);

        // Act & Assert
        $this->expectException(Exception::class);
        $this->expectExceptionMessage('User not authenticated');
        $action->execute($request);
    }
}
