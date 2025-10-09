<?php

declare(strict_types=1);

namespace Tests\Unit\Auth;

use App\Modules\Auth\Application\Actions\ResetPasswordAction;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Password;
use Mockery;
use Tests\TestCase;

class ResetPasswordActionTest extends TestCase
{
    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }

    public function test_execute_successful_password_reset(): void
    {
        // Arrange
        $request = Mockery::mock(Request::class);
        $request->shouldReceive('validate')
            ->with([
                'token' => 'required',
                'email' => 'required|email',
                'password' => 'required|min:8|confirmed',
            ])
            ->andReturn(true);
        
        $request->shouldReceive('only')
            ->with('email', 'password', 'password_confirmation', 'token')
            ->andReturn([
                'email' => 'test@example.com',
                'password' => 'newpassword123',
                'password_confirmation' => 'newpassword123',
                'token' => 'valid-token',
            ]);

        Password::shouldReceive('reset')
            ->andReturn(Password::PASSWORD_RESET);

        $action = new ResetPasswordAction();

        // Act
        $result = $action->execute($request);

        // Assert
        $this->assertEquals('passwords.reset', $result);
    }

    public function test_execute_user_not_found(): void
    {
        // Arrange
        $request = Mockery::mock(Request::class);
        $request->shouldReceive('validate')
            ->with([
                'token' => 'required',
                'email' => 'required|email',
                'password' => 'required|min:8|confirmed',
            ])
            ->andReturn(true);
        
        $request->shouldReceive('only')
            ->with('email', 'password', 'password_confirmation', 'token')
            ->andReturn([
                'email' => 'test@example.com',
                'password' => 'newpassword123',
                'password_confirmation' => 'newpassword123',
                'token' => 'invalid-token',
            ]);

        Password::shouldReceive('reset')
            ->andReturn('passwords.token');

        $action = new ResetPasswordAction();

        // Act & Assert
        $this->expectException(Exception::class);
        $this->expectExceptionMessage('Failed to reset password');
        $action->execute($request);
    }
}
