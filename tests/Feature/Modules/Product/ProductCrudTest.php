<?php

declare(strict_types=1);

namespace Tests\Feature\Modules\Product;

use App\Modules\Product\Models\Product;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProductCrudTest extends TestCase
{
    use RefreshDatabase;

    public function test_can_create_product(): void
    {
        $payload = [
            'name' => 'Test Name',
            'price' => 123.45,
            'description' => 'Test description',
        ];
        $response = $this->postJson('/api/v1/products', $payload);
        $response->assertStatus(200)
            ->assertJsonStructure(['data' => ['id', 'created_at', 'updated_at']]);
        $this->assertDatabaseHas('products', $payload);
    }

    public function test_can_list_products(): void
    {
        Product::factory()->count(2)->create();
        $response = $this->getJson('/api/v1/products');
        $response->assertStatus(200)
            ->assertJsonStructure(['data' => [['id', 'created_at', 'updated_at']]]);
    }

    public function test_can_show_product(): void
    {
        $product = Product::factory()->create();
        $response = $this->getJson("/api/v1/products/{$product->id}");
        $response->assertStatus(200)
            ->assertJsonStructure(['data' => ['id', 'created_at', 'updated_at']]);
    }

    public function test_can_update_product(): void
    {
        $product = Product::factory()->create();
        $payload = [
            'name' => 'Updated Name',
            'price' => 99.99,
            'description' => 'Updated description',
        ];
        $response = $this->putJson("/api/v1/products/{$product->id}", $payload);
        $response->assertStatus(200)
            ->assertJsonStructure(['data' => ['id', 'created_at', 'updated_at']]);
        $this->assertDatabaseHas('products', array_merge(['id' => $product->id], $payload));
    }

    public function test_can_delete_product(): void
    {
        $product = Product::factory()->create();
        $response = $this->deleteJson("/api/v1/products/{$product->id}");
        $response->assertStatus(200)
            ->assertJson(['message' => 'Product deleted']);
        $this->assertDatabaseMissing('products', ['id' => $product->id]);
    }
}
