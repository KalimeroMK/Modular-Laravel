<?php

declare(strict_types=1);

namespace Tests\Unit\Auth;

use App\Modules\Auth\Application\Actions\ResetPasswordAction;
use App\Modules\Auth\Infrastructure\Http\Requests\ResetPasswordRequest;
use Illuminate\Support\Facades\Password;
use Mockery;
use Override;
use Tests\TestCase;

class ResetPasswordActionTest extends TestCase
{
    #[Override]
    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }

    public function test_execute_successful_password_reset(): void
    {
        // Arrange
        $request = Mockery::mock(ResetPasswordRequest::class);
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
        $request = Mockery::mock(ResetPasswordRequest::class);
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
        $this->expectException(\Illuminate\Validation\ValidationException::class);
        $action->execute($request);
    }
}
