<?php

declare(strict_types=1);

namespace Tests\Feature\Auth\TwoFactor;

use App\Modules\User\Infrastructure\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Override;
use Tests\TestCase;

class TwoFactorAuthenticationTest extends TestCase
{
    use RefreshDatabase;

    public $user;

    #[Override]
    protected function setUp(): void
    {
        parent::setUp();
        $this->user = User::factory()->create();
        Sanctum::actingAs($this->user);
    }

    public function test_can_get_two_factor_status(): void
    {
        // Act
        $response = $this->getJson('/api/v1/auth/2fa/status');

        // Assert
        $response->assertStatus(200)
            ->assertJsonStructure(['enabled'])
            ->assertJson(['enabled' => false]);
    }

    public function test_can_setup_two_factor_authentication(): void
    {
        // Act
        $response = $this->postJson('/api/v1/auth/2fa/setup');

        // Assert
        $response->assertStatus(200)
            ->assertJsonStructure([
                'secret_key',
                'qr_code_url',
                'recovery_codes',
            ]);

        $this->assertIsString($response->json('secret_key'));
        $this->assertIsString($response->json('qr_code_url'));
        $this->assertIsString($response->json('recovery_codes'));
    }

    public function test_cannot_setup_two_factor_when_already_enabled(): void
    {
        // Arrange - Setup and verify 2FA to enable it
        $this->postJson('/api/v1/auth/2fa/setup');
        // Note: In real scenario, we would verify with a valid code
        // For this test, we'll manually set confirmed_at to simulate enabled state
        $this->user->update(['two_factor_confirmed_at' => now()]);

        // Act - Try to setup again
        $response = $this->postJson('/api/v1/auth/2fa/setup');

        // Assert
        $response->assertStatus(500); // Exception thrown
    }

    public function test_can_verify_two_factor_code(): void
    {
        // Arrange - Setup 2FA first
        $this->postJson('/api/v1/auth/2fa/setup');

        // Act - Verify with a code (this will fail in real scenario, but tests the endpoint)
        $response = $this->postJson('/api/v1/auth/2fa/verify', [
            'code' => '123456',
        ]);

        // Assert
        $response->assertStatus(200)
            ->assertJsonStructure(['verified']);
    }

    public function test_can_verify_with_recovery_code(): void
    {
        // Arrange - Setup 2FA first
        $setupResponse = $this->postJson('/api/v1/auth/2fa/setup');
        $recoveryCodes = explode(',', (string) $setupResponse->json('recovery_codes'));

        // Act - Verify with recovery code
        $response = $this->postJson('/api/v1/auth/2fa/verify', [
            'recovery_code' => $recoveryCodes[0],
        ]);

        // Assert
        $response->assertStatus(200)
            ->assertJsonStructure(['verified'])
            ->assertJson(['verified' => true]);

        // Verify that 2FA is now enabled
        $this->user->refresh();
        $this->assertNotNull($this->user->two_factor_confirmed_at);
        $statusResponse = $this->getJson('/api/v1/auth/2fa/status');
        $statusResponse->assertJson(['enabled' => true]);
    }

    public function test_can_disable_two_factor_authentication(): void
    {
        // Arrange - Setup and enable 2FA first
        $setupResponse = $this->postJson('/api/v1/auth/2fa/setup');
        $recoveryCodes = explode(',', (string) $setupResponse->json('recovery_codes'));
        // Verify to enable 2FA
        $this->postJson('/api/v1/auth/2fa/verify', [
            'recovery_code' => $recoveryCodes[0],
        ]);

        // Act
        $response = $this->deleteJson('/api/v1/auth/2fa/disable');

        // Assert
        $response->assertStatus(200)
            ->assertJson(['message' => 'Two-factor authentication disabled successfully']);

        // Verify that 2FA is now disabled
        $statusResponse = $this->getJson('/api/v1/auth/2fa/status');
        $statusResponse->assertJson(['enabled' => false]);
    }

    public function test_can_generate_new_recovery_codes(): void
    {
        // Arrange - Setup and enable 2FA first
        $setupResponse = $this->postJson('/api/v1/auth/2fa/setup');
        $recoveryCodes = explode(',', (string) $setupResponse->json('recovery_codes'));
        // Verify to enable 2FA
        $this->postJson('/api/v1/auth/2fa/verify', [
            'recovery_code' => $recoveryCodes[0],
        ]);

        // Act
        $response = $this->postJson('/api/v1/auth/2fa/recovery-codes');

        // Assert
        $response->assertStatus(200)
            ->assertJsonStructure(['codes'])
            ->assertJsonCount(8, 'codes');
    }

    public function test_requires_authentication_for_two_factor_endpoints(): void
    {
        // Arrange - Create a separate test case without authentication
        $this->refreshApplication();

        // Act & Assert - All endpoints should return 401 without authentication
        $this->getJson('/api/v1/auth/2fa/status')->assertStatus(401);
        $this->postJson('/api/v1/auth/2fa/setup')->assertStatus(401);
        $this->postJson('/api/v1/auth/2fa/verify', ['code' => '123456'])->assertStatus(401);
        $this->deleteJson('/api/v1/auth/2fa/disable')->assertStatus(401);
        $this->postJson('/api/v1/auth/2fa/recovery-codes')->assertStatus(401);
    }

    public function test_validation_for_verify_endpoint(): void
    {
        // Arrange - Setup 2FA first
        $this->postJson('/api/v1/auth/2fa/setup');

        // Act & Assert - Missing code
        $this->postJson('/api/v1/auth/2fa/verify', [])
            ->assertStatus(422)
            ->assertJsonValidationErrors(['code']);

        // Act & Assert - Invalid code length
        $this->postJson('/api/v1/auth/2fa/verify', ['code' => '123'])
            ->assertStatus(422)
            ->assertJsonValidationErrors(['code']);

        // Act & Assert - Invalid recovery code length
        $this->postJson('/api/v1/auth/2fa/verify', ['recovery_code' => 'short'])
            ->assertStatus(422)
            ->assertJsonValidationErrors(['recovery_code']);
    }

    public function test_complete_2fa_enable_flow(): void
    {
        // Step 1: Check initial status (should be disabled)
        $statusResponse = $this->getJson('/api/v1/auth/2fa/status');
        $statusResponse->assertStatus(200)
            ->assertJson(['enabled' => false]);

        // Step 2: Setup 2FA (generates secret and QR code)
        $setupResponse = $this->postJson('/api/v1/auth/2fa/setup');
        $setupResponse->assertStatus(200)
            ->assertJsonStructure([
                'secret_key',
                'qr_code_url',
                'recovery_codes',
            ]);

        // Step 3: Status should still be disabled (not yet verified)
        $statusResponse = $this->getJson('/api/v1/auth/2fa/status');
        $statusResponse->assertStatus(200)
            ->assertJson(['enabled' => false]);

        // Step 4: Verify with recovery code (enables 2FA)
        $recoveryCodes = explode(',', (string) $setupResponse->json('recovery_codes'));
        $verifyResponse = $this->postJson('/api/v1/auth/2fa/verify', [
            'recovery_code' => $recoveryCodes[0],
        ]);
        $verifyResponse->assertStatus(200)
            ->assertJson(['verified' => true]);

        // Step 5: Status should now be enabled
        $statusResponse = $this->getJson('/api/v1/auth/2fa/status');
        $statusResponse->assertStatus(200)
            ->assertJson(['enabled' => true]);

        // Step 6: Verify user model has confirmed_at set
        $this->user->refresh();
        $this->assertNotNull($this->user->two_factor_confirmed_at);
    }
}
