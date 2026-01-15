<?php

declare(strict_types=1);

namespace Tests\Unit\Auth;

use App\Modules\Auth\Application\DTO\SessionTokenDTO;
use App\Modules\Auth\Application\Services\IssueTokenService;
use App\Modules\User\Infrastructure\Models\User;
use Laravel\Sanctum\PersonalAccessToken;
use Mockery;
use Override;
use Tests\TestCase;

class IssueTokenServiceTest extends TestCase
{
    #[Override]
    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }

    public function test_issue_token_success(): void
    {
        // Arrange
        $user = Mockery::mock(User::class);
        $user->shouldReceive('setAttribute')->andReturnSelf();
        $user->id = 1;
        $user->name = 'Test User';
        $user->email = 'test@example.com';

        $token = Mockery::mock(PersonalAccessToken::class);
        $token->shouldReceive('getAttribute')
            ->with('plainTextToken')
            ->andReturn('test-token');

        $user->shouldReceive('createToken')
            ->with('auth_token')
            ->andReturn($token);

        $service = new IssueTokenService();

        // Act
        $result = $service->issueToken($user);

        // Assert
        $this->assertInstanceOf(SessionTokenDTO::class, $result);
        $this->assertEquals('test-token', $result->token);
    }

    public function test_revoke_token_success(): void
    {
        // Arrange
        $user = Mockery::mock(User::class);
        $user->shouldReceive('setAttribute')->andReturnSelf();
        $user->id = 1;

        $token = Mockery::mock(PersonalAccessToken::class);
        $token->shouldReceive('delete')
            ->andReturn(true);

        $user->shouldReceive('currentAccessToken')
            ->andReturn($token);

        $service = new IssueTokenService();

        // Act
        $result = $service->revokeToken($user);

        // Assert
        $this->assertTrue($result);
    }

    public function test_revoke_all_tokens_success(): void
    {
        // Arrange
        $user = Mockery::mock(User::class);
        $user->shouldReceive('setAttribute')->andReturnSelf();
        $user->id = 1;

        $user->shouldReceive('tokens')
            ->andReturnSelf();
        $user->shouldReceive('delete')
            ->andReturn(true);

        $service = new IssueTokenService();

        // Act
        $result = $service->revokeAllTokens($user);

        // Assert
        $this->assertTrue($result);
    }
}
