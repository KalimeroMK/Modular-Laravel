<?php

declare(strict_types=1);

namespace Tests\Feature\Modules\TestStub;

use App\Modules\TestStub\Models\TestStub;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TestStubCrudTest extends TestCase
{
    use RefreshDatabase;

    public function test_can_create_teststub(): void
    {
        $payload = [
            'name' => 'Test Name',
            'description' => 'Test description',
        ];
        $response = $this->postJson('/api/v1/teststubs', $payload);
        $response->assertStatus(200)
            ->assertJsonStructure(['data' => ['id', 'created_at', 'updated_at']]);
        $this->assertDatabaseHas('test_stubs', $payload);
    }

    public function test_can_list_teststubs(): void
    {
        TestStub::factory()->count(2)->create();
        $response = $this->getJson('/api/v1/teststubs');
        $response->assertStatus(200)
            ->assertJsonStructure(['data' => [['id', 'created_at', 'updated_at']]]);
    }

    public function test_can_show_teststub(): void
    {
        $teststub = TestStub::factory()->create();
        $response = $this->getJson("/api/v1/teststubs/{$teststub->id}");
        $response->assertStatus(200)
            ->assertJsonStructure(['data' => ['id', 'created_at', 'updated_at']]);
    }

    public function test_can_update_teststub(): void
    {
        $teststub = TestStub::factory()->create();
        $payload = [
            'name' => 'Updated Name',
            'description' => 'Updated description',
        ];
        $response = $this->putJson("/api/v1/teststubs/{$teststub->id}", $payload);
        $response->assertStatus(200)
            ->assertJsonStructure(['data' => ['id', 'created_at', 'updated_at']]);
        $this->assertDatabaseHas('test_stubs', array_merge(['id' => $teststub->id], $payload));
    }

    public function test_can_delete_teststub(): void
    {
        $teststub = TestStub::factory()->create();
        $response = $this->deleteJson("/api/v1/teststubs/{$teststub->id}");
        $response->assertStatus(200)
            ->assertJson(['message' => 'TestStub deleted']);
        $this->assertDatabaseMissing('test_stubs', ['id' => $teststub->id]);
    }
}
