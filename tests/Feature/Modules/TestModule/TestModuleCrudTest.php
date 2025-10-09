<?php

declare(strict_types=1);

namespace Tests\Feature\Modules\TestModule;

use App\Modules\TestModule\Models\TestModule;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TestModuleCrudTest extends TestCase
{
    use RefreshDatabase;

    public function test_can_create_testmodule(): void
    {
        $payload = [
            'name' => 'Test Name',
            'description' => 'Test description',
        ];
        $response = $this->postJson('/api/v1/testmodules', $payload);
        $response->assertStatus(200)
            ->assertJsonStructure(['data' => ['id', 'created_at', 'updated_at']]);
        $this->assertDatabaseHas('test_modules', $payload);
    }

    public function test_can_list_testmodules(): void
    {
        TestModule::factory()->count(2)->create();
        $response = $this->getJson('/api/v1/testmodules');
        $response->assertStatus(200)
            ->assertJsonStructure(['data' => [['id', 'created_at', 'updated_at']]]);
    }

    public function test_can_show_testmodule(): void
    {
        $testmodule = TestModule::factory()->create();
        $response = $this->getJson("/api/v1/testmodules/{$testmodule->id}");
        $response->assertStatus(200)
            ->assertJsonStructure(['data' => ['id', 'created_at', 'updated_at']]);
    }

    public function test_can_update_testmodule(): void
    {
        $testmodule = TestModule::factory()->create();
        $payload = [
            'name' => 'Updated Name',
            'description' => 'Updated description',
        ];
        $response = $this->putJson("/api/v1/testmodules/{$testmodule->id}", $payload);
        $response->assertStatus(200)
            ->assertJsonStructure(['data' => ['id', 'created_at', 'updated_at']]);
        $this->assertDatabaseHas('test_modules', array_merge(['id' => $testmodule->id], $payload));
    }

    public function test_can_delete_testmodule(): void
    {
        $testmodule = TestModule::factory()->create();
        $response = $this->deleteJson("/api/v1/testmodules/{$testmodule->id}");
        $response->assertStatus(200)
            ->assertJson(['message' => 'TestModule deleted']);
        $this->assertDatabaseMissing('test_modules', ['id' => $testmodule->id]);
    }
}
