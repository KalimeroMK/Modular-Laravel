<?php

declare(strict_types=1);

namespace Tests\Unit\Auth\TwoFactor;

use App\Modules\Auth\Application\Actions\TwoFactor\VerifyAction;
use App\Modules\Auth\Application\DTO\TwoFactor\VerificationDTO;
use App\Modules\Auth\Application\Services\TwoFactor\ServiceInterface;
use App\Modules\User\Infrastructure\Models\User;
use Exception;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Mockery;
use Override;
use Tests\TestCase;

class VerifyActionTest extends TestCase
{
    use RefreshDatabase;

    #[Override]
    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }

    public function test_execute_successful_verification(): void
    {
        // Arrange
        $user = User::factory()->create(['two_factor_secret' => 'encrypted_secret']);
        $dto = new VerificationDTO('123456');

        $twoFactorService = Mockery::mock(ServiceInterface::class);
        $twoFactorService->shouldReceive('verifyTwoFactor')
            ->with($user, $dto)
            ->andReturn(true);

        $action = new VerifyAction($twoFactorService);

        // Act
        $result = $action->execute($user, $dto);

        // Assert
        $this->assertTrue($result);
    }

    public function test_execute_throws_exception_when_secret_not_set(): void
    {
        // Arrange
        $user = User::factory()->create(['two_factor_secret' => null]);
        $dto = new VerificationDTO('123456');

        $twoFactorService = Mockery::mock(ServiceInterface::class);

        $action = new VerifyAction($twoFactorService);

        // Act & Assert
        $this->expectException(Exception::class);
        $this->expectExceptionMessage('Two-factor authentication secret is not set. Please run setup first.');
        $action->execute($user, $dto);
    }
}
