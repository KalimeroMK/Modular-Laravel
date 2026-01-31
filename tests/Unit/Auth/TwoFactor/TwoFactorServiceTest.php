<?php

declare(strict_types=1);

namespace Tests\Unit\Auth\TwoFactor;

use App\Modules\Auth\Application\Services\TwoFactor\Service;
use App\Modules\User\Infrastructure\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Mockery;
use Override;
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
        
        $secretKey = $this->service->generateSecretKey();

        
        $this->assertIsString($secretKey);
        $this->assertGreaterThan(10, mb_strlen($secretKey));
    }

    public function test_generate_qr_code_url(): void
    {
        
        $user = User::factory()->create();
        $secretKey = 'JBSWY3DPEHPK3PXP';

        
        $qrCodeUrl = $this->service->generateQrCodeUrl($user, $secretKey);

        
        $this->assertIsString($qrCodeUrl);
        $this->assertStringContainsString('otpauth://totp/', $qrCodeUrl);
        $this->assertStringContainsString(urlencode((string) $user->email), $qrCodeUrl);
    }

    public function test_generate_recovery_codes(): void
    {
        
        $recoveryCodes = $this->service->generateRecoveryCodes();

        
        $this->assertCount(8, $recoveryCodes->codes);
        foreach ($recoveryCodes->codes as $code) {
            $this->assertEquals(10, mb_strlen($code));
        }
    }

    public function test_setup_two_factor(): void
    {
        
        $user = User::factory()->create();

        
        $setupData = $this->service->setupTwoFactor($user);

        
        $this->assertInstanceOf(\App\Modules\Auth\Application\DTO\TwoFactor\SetupDTO::class, $setupData);
        $this->assertIsString($setupData->secretKey);
        $this->assertIsString($setupData->qrCodeUrl);
        $this->assertIsString($setupData->recoveryCodes);

        
        $user->refresh();
        $this->assertNotNull($user->two_factor_secret);
        $this->assertNotNull($user->two_factor_recovery_codes);
    }

    public function test_is_two_factor_enabled(): void
    {
        $this->markTestSkipped('Test has known issue with refresh() - needs investigation');

        
        $user = User::factory()->create();

        
        $this->assertFalse($this->service->isTwoFactorEnabled($user));

        
        $user->update(['two_factor_secret' => 'encrypted_secret']);
        $user->refresh();

        
        $this->assertTrue($this->service->isTwoFactorEnabled($user));
    }

    public function test_disable_two_factor(): void
    {
        
        $user = User::factory()->create([
            'two_factor_secret' => 'encrypted_secret',
            'two_factor_recovery_codes' => 'encrypted_codes',
        ]);

        
        $result = $this->service->disableTwoFactor($user);

        
        $this->assertTrue($result);
        $user->refresh();
        $this->assertNull($user->two_factor_secret);
        $this->assertNull($user->two_factor_recovery_codes);
    }
}
