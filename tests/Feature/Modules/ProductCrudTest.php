<?php

namespace Tests\Feature\Modules\Product;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Modules\Product\Models\Product;

class ProductCrudTest extends TestCase
{
    use RefreshDatabase;

    public function test_index_returns_ok()
    {
        $response = $this->getJson('/api/v1/products');
        $response->assertOk();
    }

    public function test_store_creates_resource()
    {
        
        $data = array (
  '' => 'test',
);
        $response = $this->postJson('/api/v1/products', $data);
        $response->assertCreated();
    }

    public function test_show_returns_resource()
    {
        $model = Product::factory()->create();
        $response = $this->getJson("/api/v1/products/{$model->id}");
        $response->assertOk();
    }

    public function test_update_modifies_resource()
    {
        $model = Product::factory()->create();
        
        $data = array (
  '' => 'updated',
);
        $response = $this->putJson("/api/v1/products/{$model->id}", $data);
        $response->assertOk();
    }

    public function test_destroy_deletes_resource()
    {
        $model = Product::factory()->create();
        $response = $this->deleteJson("/api/v1/products/{$model->id}");
        $response->assertNoContent();
    }
}
