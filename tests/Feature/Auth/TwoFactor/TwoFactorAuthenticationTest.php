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

    
    protected function setUp(): void
    {
        parent::setUp();
        $this->user = User::factory()->create();
        Sanctum::actingAs($this->user);
    }

    public function test_can_get_two_factor_status(): void
    {
        
        $response = $this->getJson('/api/v1/auth/2fa/status');

        
        $response->assertStatus(200)
            ->assertJsonStructure(['enabled'])
            ->assertJson(['enabled' => false]);
    }

    public function test_can_setup_two_factor_authentication(): void
    {
        
        $response = $this->postJson('/api/v1/auth/2fa/setup');

        
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
        
        $this->postJson('/api/v1/auth/2fa/setup');
        
        
        $this->user->update(['two_factor_confirmed_at' => now()]);

        
        $response = $this->postJson('/api/v1/auth/2fa/setup');

        
        $response->assertStatus(400)
            ->assertJson([
                'status' => 'error',
                'error_code' => 'TWO_FACTOR_ALREADY_ENABLED',
            ]);
    }

    public function test_can_verify_two_factor_code(): void
    {
        
        $setupResponse = $this->postJson('/api/v1/auth/2fa/setup');
        $setupResponse->assertStatus(200);

        
        $response = $this->postJson('/api/v1/auth/2fa/verify', [
            'code' => '123456',
        ]);

        
        $response->assertStatus(400)
            ->assertJson([
                'status' => 'error',
                'error_code' => 'TWO_FACTOR_INVALID_CODE',
            ]);
    }

    public function test_cannot_verify_without_setup(): void
    {
        
        $response = $this->postJson('/api/v1/auth/2fa/verify', [
            'code' => '123456',
        ]);

        
        $response->assertStatus(400)
            ->assertJson([
                'status' => 'error',
                'error_code' => 'TWO_FACTOR_SECRET_NOT_SET',
            ]);
    }

    public function test_cannot_disable_when_not_enabled(): void
    {
        
        $response = $this->deleteJson('/api/v1/auth/2fa/disable');

        
        $response->assertStatus(400)
            ->assertJson([
                'status' => 'error',
                'error_code' => 'TWO_FACTOR_NOT_ENABLED',
            ]);
    }

    public function test_cannot_generate_recovery_codes_when_not_enabled(): void
    {
        
        $response = $this->postJson('/api/v1/auth/2fa/recovery-codes');

        
        $response->assertStatus(400)
            ->assertJson([
                'status' => 'error',
                'error_code' => 'TWO_FACTOR_NOT_ENABLED',
            ]);
    }

    public function test_can_verify_with_recovery_code(): void
    {
        
        $setupResponse = $this->postJson('/api/v1/auth/2fa/setup');
        $recoveryCodes = explode(',', (string) $setupResponse->json('recovery_codes'));

        
        $response = $this->postJson('/api/v1/auth/2fa/verify', [
            'recovery_code' => $recoveryCodes[0],
        ]);

        
        $response->assertStatus(200)
            ->assertJsonStructure(['verified'])
            ->assertJson(['verified' => true]);

        
        $this->user->refresh();
        $this->assertNotNull($this->user->two_factor_confirmed_at);
        $statusResponse = $this->getJson('/api/v1/auth/2fa/status');
        $statusResponse->assertJson(['enabled' => true]);
    }

    public function test_can_disable_two_factor_authentication(): void
    {
        
        $setupResponse = $this->postJson('/api/v1/auth/2fa/setup');
        $recoveryCodes = explode(',', (string) $setupResponse->json('recovery_codes'));
        
        $this->postJson('/api/v1/auth/2fa/verify', [
            'recovery_code' => $recoveryCodes[0],
        ]);

        
        $response = $this->deleteJson('/api/v1/auth/2fa/disable');

        
        $response->assertStatus(200)
            ->assertJson(['message' => 'Two-factor authentication disabled successfully']);

        
        $statusResponse = $this->getJson('/api/v1/auth/2fa/status');
        $statusResponse->assertJson(['enabled' => false]);
    }

    public function test_can_generate_new_recovery_codes(): void
    {
        
        $setupResponse = $this->postJson('/api/v1/auth/2fa/setup');
        $recoveryCodes = explode(',', (string) $setupResponse->json('recovery_codes'));
        
        $this->postJson('/api/v1/auth/2fa/verify', [
            'recovery_code' => $recoveryCodes[0],
        ]);

        
        $response = $this->postJson('/api/v1/auth/2fa/recovery-codes');

        
        $response->assertStatus(200)
            ->assertJsonStructure(['codes'])
            ->assertJsonCount(8, 'codes');
    }

    public function test_requires_authentication_for_two_factor_endpoints(): void
    {
        
        $this->refreshApplication();

        
        $this->getJson('/api/v1/auth/2fa/status')->assertStatus(401);
        $this->postJson('/api/v1/auth/2fa/setup')->assertStatus(401);
        $this->postJson('/api/v1/auth/2fa/verify', ['code' => '123456'])->assertStatus(401);
        $this->deleteJson('/api/v1/auth/2fa/disable')->assertStatus(401);
        $this->postJson('/api/v1/auth/2fa/recovery-codes')->assertStatus(401);
    }

    public function test_validation_for_verify_endpoint(): void
    {
        
        $this->postJson('/api/v1/auth/2fa/setup');

        
        $this->postJson('/api/v1/auth/2fa/verify', [])
            ->assertStatus(422)
            ->assertJsonValidationErrors(['code']);

        
        $this->postJson('/api/v1/auth/2fa/verify', ['code' => '123'])
            ->assertStatus(422)
            ->assertJsonValidationErrors(['code']);

        
        $this->postJson('/api/v1/auth/2fa/verify', ['recovery_code' => 'short'])
            ->assertStatus(422)
            ->assertJsonValidationErrors(['recovery_code']);
    }

    public function test_complete_2fa_enable_flow(): void
    {
        
        $statusResponse = $this->getJson('/api/v1/auth/2fa/status');
        $statusResponse->assertStatus(200)
            ->assertJson(['enabled' => false]);

        
        $setupResponse = $this->postJson('/api/v1/auth/2fa/setup');
        $setupResponse->assertStatus(200)
            ->assertJsonStructure([
                'secret_key',
                'qr_code_url',
                'recovery_codes',
            ]);

        
        $statusResponse = $this->getJson('/api/v1/auth/2fa/status');
        $statusResponse->assertStatus(200)
            ->assertJson(['enabled' => false]);

        
        $recoveryCodes = explode(',', (string) $setupResponse->json('recovery_codes'));
        $verifyResponse = $this->postJson('/api/v1/auth/2fa/verify', [
            'recovery_code' => $recoveryCodes[0],
        ]);
        $verifyResponse->assertStatus(200)
            ->assertJson(['verified' => true]);

        
        $statusResponse = $this->getJson('/api/v1/auth/2fa/status');
        $statusResponse->assertStatus(200)
            ->assertJson(['enabled' => true]);

        
        $this->user->refresh();
        $this->assertNotNull($this->user->two_factor_confirmed_at);
    }
}
