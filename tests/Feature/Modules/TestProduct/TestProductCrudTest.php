<?php

declare(strict_types=1);

namespace Tests\Feature\Modules\TestProduct;

use App\Modules\TestProduct\Models\TestProduct;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TestProductCrudTest extends TestCase
{
    use RefreshDatabase;

    public function test_can_create_testproduct(): void
    {
        $payload = [
            'name' => 'Test Name',
            'price' => 123.45,
        ];
        $response = $this->postJson('/api/v1/testproducts', $payload);
        $response->assertStatus(200)
            ->assertJsonStructure(['data' => ['id', 'created_at', 'updated_at']]);
        $this->assertDatabaseHas('test_products', $payload);
    }

    public function test_can_list_testproducts(): void
    {
        TestProduct::factory()->count(2)->create();
        $response = $this->getJson('/api/v1/testproducts');
        $response->assertStatus(200)
            ->assertJsonStructure(['data' => [['id', 'created_at', 'updated_at']]]);
    }

    public function test_can_show_testproduct(): void
    {
        $testproduct = TestProduct::factory()->create();
        $response = $this->getJson("/api/v1/testproducts/{$testproduct->id}");
        $response->assertStatus(200)
            ->assertJsonStructure(['data' => ['id', 'created_at', 'updated_at']]);
    }

    public function test_can_update_testproduct(): void
    {
        $testproduct = TestProduct::factory()->create();
        $payload = [
            'name' => 'Updated Name',
            'price' => 99.99,
        ];
        $response = $this->putJson("/api/v1/testproducts/{$testproduct->id}", $payload);
        $response->assertStatus(200)
            ->assertJsonStructure(['data' => ['id', 'created_at', 'updated_at']]);
        $this->assertDatabaseHas('test_products', array_merge(['id' => $testproduct->id], $payload));
    }

    public function test_can_delete_testproduct(): void
    {
        $testproduct = TestProduct::factory()->create();
        $response = $this->deleteJson("/api/v1/testproducts/{$testproduct->id}");
        $response->assertStatus(200)
            ->assertJson(['message' => 'TestProduct deleted']);
        $this->assertDatabaseMissing('test_products', ['id' => $testproduct->id]);
    }
}
