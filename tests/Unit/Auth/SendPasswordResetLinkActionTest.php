<?php

declare(strict_types=1);

namespace Tests\Unit\Auth;

use App\Modules\Auth\Application\Actions\SendPasswordResetLinkAction;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Password;
use Mockery;
use Tests\TestCase;

class SendPasswordResetLinkActionTest extends TestCase
{
    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }

    public function test_execute_successful_password_reset_link(): void
    {
        // Arrange
        $request = Mockery::mock(Request::class);
        $request->shouldReceive('validate')
            ->with(['email' => 'required|email'])
            ->andReturn(true);

        $request->shouldReceive('only')
            ->with('email')
            ->andReturn(['email' => 'test@example.com']);

        Password::shouldReceive('sendResetLink')
            ->with(['email' => 'test@example.com'])
            ->andReturn(Password::RESET_LINK_SENT);

        $action = new SendPasswordResetLinkAction();

        // Act
        $result = $action->execute($request);

        // Assert
        $this->assertEquals('passwords.sent', $result);
    }

    public function test_execute_user_not_found(): void
    {
        // Arrange
        $request = Mockery::mock(Request::class);
        $request->shouldReceive('validate')
            ->with(['email' => 'required|email'])
            ->andReturn(true);

        $request->shouldReceive('only')
            ->with('email')
            ->andReturn(['email' => 'test@example.com']);

        Password::shouldReceive('sendResetLink')
            ->with(['email' => 'test@example.com'])
            ->andReturn('error');

        $action = new SendPasswordResetLinkAction();

        // Act & Assert
        $this->expectException(Exception::class);
        $this->expectExceptionMessage('Failed to send reset link');
        $action->execute($request);
    }

    public function test_execute_password_reset_failure(): void
    {
        // Arrange
        $request = Mockery::mock(Request::class);
        $request->shouldReceive('validate')
            ->with(['email' => 'required|email'])
            ->andReturn(true);

        $request->shouldReceive('only')
            ->with('email')
            ->andReturn(['email' => 'test@example.com']);

        Password::shouldReceive('sendResetLink')
            ->with(['email' => 'test@example.com'])
            ->andReturn('error');

        $action = new SendPasswordResetLinkAction();

        // Act & Assert
        $this->expectException(Exception::class);
        $this->expectExceptionMessage('Failed to send reset link');
        $action->execute($request);
    }
}
