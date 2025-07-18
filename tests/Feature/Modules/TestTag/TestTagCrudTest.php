<?php

declare(strict_types=1);

namespace Tests\Feature\Modules\TestTag;

use App\Modules\TestTag\Models\TestTag;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TestTagCrudTest extends TestCase
{
    use RefreshDatabase;

    public function test_can_list_testtags(): void
    {
        TestTag::factory()->count(2)->create();
        $response = $this->getJson('/api/v1/testtags');
        $response->assertStatus(200)
            ->assertJsonStructure(['data' => [['id']]]);
    }

    public function test_can_create_testtag(): void
    {
        $related = $this->createRelatedModels();

        $data = array_merge([
                        'name' => 'Test Name',
            'slug' => 'Test slug',
        ], $related);

        $response = $this->postJson('/api/v1/testtags', $data);
        $response->assertStatus(201)
            ->assertJsonStructure(['data' => ['id']]);
        
        // Verify in database
        $responseData = $response->json('data');
        $this->assertDatabaseHas('test_tags', ['id' => $responseData['id']]);
    }

    public function test_can_show_testtag(): void
    {
        $model = TestTag::factory()->create();
        $response = $this->getJson("/api/v1/testtags/{$model->id}");
        $response->assertStatus(200)
            ->assertJsonStructure(['data' => ['id']])
            ->assertJson(['data' => ['id' => $model->id]]);
    }

    public function test_can_update_testtag(): void
    {
        $model = TestTag::factory()->create();

        $data = array_merge([
                        'name' => 'Updated Name',
            'slug' => 'Updated slug',
        ], $this->createRelatedModels());

        $response = $this->putJson("/api/v1/testtags/{$model->id}", $data);
        $response->assertStatus(200)
            ->assertJsonStructure(['data' => ['id']])
            ->assertJson(['data' => ['id' => $model->id]]);
        
        // Verify changes in database
        $this->assertDatabaseHas('test_tags', array_merge(['id' => $model->id], $data));
    }

    public function test_can_delete_testtag(): void
    {
        $model = TestTag::factory()->create();
        $response = $this->deleteJson("/api/v1/testtags/{$model->id}");
        $response->assertStatus(200)
            ->assertJsonStructure(['message']);
        
        // Verify deletion in database
        $this->assertDatabaseMissing('test_tags', ['id' => $model->id]);
    }

    public function test_show_testtag_not_found(): void
    {
        $response = $this->getJson('/api/v1/testtags/999999');
        $response->assertStatus(404);
    }

    public function test_update_testtag_not_found(): void
    {
        $data = array_merge([
                        'name' => 'Updated Name',
            'slug' => 'Updated slug',
        ], $this->createRelatedModels());

        $response = $this->putJson('/api/v1/testtags/999999', $data);
        $response->assertStatus(404);
    }

    public function test_delete_testtag_not_found(): void
    {
        $response = $this->deleteJson('/api/v1/testtags/999999');
        $response->assertStatus(404);
    }

    protected function createRelatedModels(): array
    {
        return [];
    }
}
