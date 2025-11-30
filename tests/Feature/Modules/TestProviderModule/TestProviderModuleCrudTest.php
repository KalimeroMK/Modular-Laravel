<?php

declare(strict_types=1);

namespace Tests\Feature\Modules\TestProviderModule;

use App\Modules\TestProviderModule\Infrastructure\Models\TestProviderModule;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TestProviderModuleCrudTest extends TestCase
{
    use RefreshDatabase;

    public function test_can_create_testprovidermodule(): void
    {
        $payload = [

        ];
        $response = $this->postJson('/api/v1/testprovidermodules', $payload);
        $response->assertStatus(200)
            ->assertJsonStructure(['data' => ['id', 'created_at', 'updated_at']]);
        $this->assertDatabaseHas('test_provider_modules', $payload);
    }

    public function test_can_list_testprovidermodules(): void
    {
        TestProviderModule::factory()->count(2)->create();
        $response = $this->getJson('/api/v1/testprovidermodules');
        $response->assertStatus(200)
            ->assertJsonStructure(['data' => [['id', 'created_at', 'updated_at']]]);
    }

    public function test_can_show_testprovidermodule(): void
    {
        $testprovidermodule = TestProviderModule::factory()->create();
        $response = $this->getJson("/api/v1/testprovidermodules/{$testprovidermodule->id}");
        $response->assertStatus(200)
            ->assertJsonStructure(['data' => ['id', 'created_at', 'updated_at']]);
    }

    public function test_can_update_testprovidermodule(): void
    {
        $testprovidermodule = TestProviderModule::factory()->create();
        $payload = [

        ];
        $response = $this->putJson("/api/v1/testprovidermodules/{$testprovidermodule->id}", $payload);
        $response->assertStatus(200)
            ->assertJsonStructure(['data' => ['id', 'created_at', 'updated_at']]);
        $this->assertDatabaseHas('test_provider_modules', array_merge(['id' => $testprovidermodule->id], $payload));
    }

    public function test_can_delete_testprovidermodule(): void
    {
        $testprovidermodule = TestProviderModule::factory()->create();
        $response = $this->deleteJson("/api/v1/testprovidermodules/{$testprovidermodule->id}");
        $response->assertStatus(200)
            ->assertJson(['message' => 'TestProviderModule deleted']);
        $this->assertDatabaseMissing('test_provider_modules', ['id' => $testprovidermodule->id]);
    }
}
