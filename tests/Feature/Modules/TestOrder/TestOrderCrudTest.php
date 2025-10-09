<?php

declare(strict_types=1);

namespace Tests\Feature\Modules\TestOrder;

use App\Modules\TestOrder\Models\TestOrder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TestOrderCrudTest extends TestCase
{
    use RefreshDatabase;

    public function test_can_create_testorder(): void
    {
        $payload = [
            'total' => 123.45,
            'status' => 'Test status',
        ];
        $response = $this->postJson('/api/v1/testorders', $payload);
        $response->assertStatus(200)
            ->assertJsonStructure(['data' => ['id', 'created_at', 'updated_at']]);
        $this->assertDatabaseHas('test_orders', $payload);
    }

    public function test_can_list_testorders(): void
    {
        TestOrder::factory()->count(2)->create();
        $response = $this->getJson('/api/v1/testorders');
        $response->assertStatus(200)
            ->assertJsonStructure(['data' => [['id', 'created_at', 'updated_at']]]);
    }

    public function test_can_show_testorder(): void
    {
        $testorder = TestOrder::factory()->create();
        $response = $this->getJson("/api/v1/testorders/{$testorder->id}");
        $response->assertStatus(200)
            ->assertJsonStructure(['data' => ['id', 'created_at', 'updated_at']]);
    }

    public function test_can_update_testorder(): void
    {
        $testorder = TestOrder::factory()->create();
        $payload = [
            'total' => 99.99,
            'status' => 'Updated status',
        ];
        $response = $this->putJson("/api/v1/testorders/{$testorder->id}", $payload);
        $response->assertStatus(200)
            ->assertJsonStructure(['data' => ['id', 'created_at', 'updated_at']]);
        $this->assertDatabaseHas('test_orders', array_merge(['id' => $testorder->id], $payload));
    }

    public function test_can_delete_testorder(): void
    {
        $testorder = TestOrder::factory()->create();
        $response = $this->deleteJson("/api/v1/testorders/{$testorder->id}");
        $response->assertStatus(200)
            ->assertJson(['message' => 'TestOrder deleted']);
        $this->assertDatabaseMissing('test_orders', ['id' => $testorder->id]);
    }
}
