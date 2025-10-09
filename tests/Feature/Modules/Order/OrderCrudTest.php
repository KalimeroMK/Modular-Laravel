<?php

declare(strict_types=1);

namespace Tests\Feature\Modules\Order;

use App\Modules\Order\Models\Order;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class OrderCrudTest extends TestCase
{
    use RefreshDatabase;

    public function test_can_create_order(): void
    {
        $payload = [
            'total' => 123.45,
            'status' => 'Test status',
            'customer_email' => 'test@example.com',
        ];
        $response = $this->postJson('/api/v1/orders', $payload);
        $response->assertStatus(200)
            ->assertJsonStructure(['data' => ['id', 'created_at', 'updated_at']]);
        $this->assertDatabaseHas('orders', $payload);
    }

    public function test_can_list_orders(): void
    {
        Order::factory()->count(2)->create();
        $response = $this->getJson('/api/v1/orders');
        $response->assertStatus(200)
            ->assertJsonStructure(['data' => [['id', 'created_at', 'updated_at']]]);
    }

    public function test_can_show_order(): void
    {
        $order = Order::factory()->create();
        $response = $this->getJson("/api/v1/orders/{$order->id}");
        $response->assertStatus(200)
            ->assertJsonStructure(['data' => ['id', 'created_at', 'updated_at']]);
    }

    public function test_can_update_order(): void
    {
        $order = Order::factory()->create();
        $payload = [
            'total' => 99.99,
            'status' => 'Updated status',
            'customer_email' => 'updated@example.com',
        ];
        $response = $this->putJson("/api/v1/orders/{$order->id}", $payload);
        $response->assertStatus(200)
            ->assertJsonStructure(['data' => ['id', 'created_at', 'updated_at']]);
        $this->assertDatabaseHas('orders', array_merge(['id' => $order->id], $payload));
    }

    public function test_can_delete_order(): void
    {
        $order = Order::factory()->create();
        $response = $this->deleteJson("/api/v1/orders/{$order->id}");
        $response->assertStatus(200)
            ->assertJson(['message' => 'Order deleted']);
        $this->assertDatabaseMissing('orders', ['id' => $order->id]);
    }
}
