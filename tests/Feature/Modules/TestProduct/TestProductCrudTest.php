<?php

declare(strict_types=1);

namespace Tests\Feature\Modules\TestProduct;

use App\Modules\TestProduct\Models\TestProduct;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TestProductCrudTest extends TestCase
{
    use RefreshDatabase;

    public function test_can_list_testproducts(): void
    {
        TestProduct::factory()->count(2)->create();
        $response = $this->getJson('/api/v1/testproducts');
        $response->assertStatus(200)
            ->assertJsonStructure(['data' => [['id']]]);
    }

    public function test_can_create_testproduct(): void
    {
        $related = $this->createRelatedModels();

        $data = array_merge([
                        'name' => 'Test Name',
        ], $related);

        $response = $this->postJson('/api/v1/testproducts', $data);
        $response->assertStatus(201)
            ->assertJsonStructure(['data' => ['id']]);
        
        // Verify in database
        $responseData = $response->json('data');
        $this->assertDatabaseHas('test_products', ['id' => $responseData['id']]);
    }

    public function test_can_show_testproduct(): void
    {
        $model = TestProduct::factory()->create();
        $response = $this->getJson("/api/v1/testproducts/{$model->id}");
        $response->assertStatus(200)
            ->assertJsonStructure(['data' => ['id']])
            ->assertJson(['data' => ['id' => $model->id]]);
    }

    public function test_can_update_testproduct(): void
    {
        $model = TestProduct::factory()->create();

        $data = array_merge([
                        'name' => 'Updated Name',
        ], $this->createRelatedModels());

        $response = $this->putJson("/api/v1/testproducts/{$model->id}", $data);
        $response->assertStatus(200)
            ->assertJsonStructure(['data' => ['id']])
            ->assertJson(['data' => ['id' => $model->id]]);
        
        // Verify changes in database
        $this->assertDatabaseHas('test_products', array_merge(['id' => $model->id], $data));
    }

    public function test_can_delete_testproduct(): void
    {
        $model = TestProduct::factory()->create();
        $response = $this->deleteJson("/api/v1/testproducts/{$model->id}");
        $response->assertStatus(200)
            ->assertJsonStructure(['message']);
        
        // Verify deletion in database
        $this->assertDatabaseMissing('test_products', ['id' => $model->id]);
    }

    public function test_show_testproduct_not_found(): void
    {
        $response = $this->getJson('/api/v1/testproducts/999999');
        $response->assertStatus(404);
    }

    public function test_update_testproduct_not_found(): void
    {
        $data = array_merge([
                        'name' => 'Updated Name',
        ], $this->createRelatedModels());

        $response = $this->putJson('/api/v1/testproducts/999999', $data);
        $response->assertStatus(404);
    }

    public function test_delete_testproduct_not_found(): void
    {
        $response = $this->deleteJson('/api/v1/testproducts/999999');
        $response->assertStatus(404);
    }

    protected function createRelatedModels(): array
    {
        return [];
    }
}
