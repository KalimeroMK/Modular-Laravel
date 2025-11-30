<?php

declare(strict_types=1);

namespace Tests\Unit\Auth\TwoFactor;

use App\Modules\Auth\Application\Actions\TwoFactor\SetupAction;
use App\Modules\Auth\Application\DTO\TwoFactor\SetupDTO;
use App\Modules\Auth\Application\Services\TwoFactor\ServiceInterface;
use App\Modules\User\Infrastructure\Models\User;
use Exception;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Mockery;
use Tests\TestCase;

class SetupActionTest extends TestCase
{
    use RefreshDatabase;

    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }

    public function test_execute_successful_setup(): void
    {
        // Arrange
        $user = User::factory()->create();
        $secretKey = 'JBSWY3DPEHPK3PXP';
        $qrCodeUrl = 'https://api.qrserver.com/v1/create-qr-code/?data=otpauth://totp/...';
        $recoveryCodes = 'code1,code2,code3';

        $setupDTO = new SetupDTO($secretKey, $qrCodeUrl, $recoveryCodes);

        $twoFactorService = Mockery::mock(ServiceInterface::class);
        $twoFactorService->shouldReceive('isTwoFactorEnabled')
            ->with($user)
            ->andReturn(false);
        $twoFactorService->shouldReceive('setupTwoFactor')
            ->with($user)
            ->andReturn($setupDTO);

        $action = new SetupAction($twoFactorService);

        // Act
        $result = $action->execute($user);

        // Assert
        $this->assertInstanceOf(SetupDTO::class, $result);
        $this->assertEquals($secretKey, $result->secretKey);
        $this->assertEquals($qrCodeUrl, $result->qrCodeUrl);
        $this->assertEquals($recoveryCodes, $result->recoveryCodes);
    }

    public function test_execute_throws_exception_when_already_enabled(): void
    {
        // Arrange
        $user = User::factory()->create();

        $twoFactorService = Mockery::mock(ServiceInterface::class);
        $twoFactorService->shouldReceive('isTwoFactorEnabled')
            ->with($user)
            ->andReturn(true);

        $action = new SetupAction($twoFactorService);

        // Act & Assert
        $this->expectException(Exception::class);
        $this->expectExceptionMessage('Two-factor authentication is already enabled for this user.');
        $action->execute($user);
    }
}
