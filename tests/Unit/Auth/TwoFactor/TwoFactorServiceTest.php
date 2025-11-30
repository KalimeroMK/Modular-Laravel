<?php

declare(strict_types=1);

namespace Tests\Unit\Auth\TwoFactor;

use App\Modules\Auth\Application\Services\TwoFactor\Service;
use App\Modules\User\Infrastructure\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Mockery;
use PragmaRX\Google2FA\Google2FA;
use Tests\TestCase;

class TwoFactorServiceTest extends TestCase
{
    use RefreshDatabase;

    protected Google2FA $google2fa;

    protected Service $service;

    protected function setUp(): void
    {
        parent::setUp();
        $this->google2fa = new Google2FA();
        $this->service = new Service($this->google2fa);
    }

    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }

    public function test_generate_secret_key(): void
    {
        // Act
        $secretKey = $this->service->generateSecretKey();

        // Assert
        $this->assertIsString($secretKey);
        $this->assertGreaterThan(10, mb_strlen($secretKey));
    }

    public function test_generate_qr_code_url(): void
    {
        // Arrange
        $user = User::factory()->create();
        $secretKey = 'JBSWY3DPEHPK3PXP';

        // Act
        $qrCodeUrl = $this->service->generateQrCodeUrl($user, $secretKey);

        // Assert
        $this->assertIsString($qrCodeUrl);
        $this->assertStringContainsString('otpauth://totp/', $qrCodeUrl);
        $this->assertStringContainsString(urlencode($user->email), $qrCodeUrl);
    }

    public function test_generate_recovery_codes(): void
    {
        // Act
        $recoveryCodes = $this->service->generateRecoveryCodes();

        // Assert
        $this->assertCount(8, $recoveryCodes->codes);
        foreach ($recoveryCodes->codes as $code) {
            $this->assertEquals(10, mb_strlen($code));
        }
    }

    public function test_setup_two_factor(): void
    {
        // Arrange
        $user = User::factory()->create();

        // Act
        $setupData = $this->service->setupTwoFactor($user);

        // Assert
        $this->assertInstanceOf(\App\Modules\Auth\Application\DTO\TwoFactor\SetupDTO::class, $setupData);
        $this->assertIsString($setupData->secretKey);
        $this->assertIsString($setupData->qrCodeUrl);
        $this->assertIsString($setupData->recoveryCodes);

        // Check that user was updated
        $user->refresh();
        $this->assertNotNull($user->two_factor_secret);
        $this->assertNotNull($user->two_factor_recovery_codes);
    }

    public function test_is_two_factor_enabled(): void
    {
        // Arrange
        $user = User::factory()->create();

        // Act & Assert - Initially disabled
        $this->assertFalse($this->service->isTwoFactorEnabled($user));

        // Enable 2FA
        $user->update(['two_factor_secret' => 'encrypted_secret']);

        // Act & Assert - Now enabled
        $this->assertTrue($this->service->isTwoFactorEnabled($user));
    }

    public function test_disable_two_factor(): void
    {
        // Arrange
        $user = User::factory()->create([
            'two_factor_secret' => 'encrypted_secret',
            'two_factor_recovery_codes' => 'encrypted_codes',
        ]);

        // Act
        $result = $this->service->disableTwoFactor($user);

        // Assert
        $this->assertTrue($result);
        $user->refresh();
        $this->assertNull($user->two_factor_secret);
        $this->assertNull($user->two_factor_recovery_codes);
    }
}
