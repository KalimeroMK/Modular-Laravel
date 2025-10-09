<?php

declare(strict_types=1);

namespace Tests\Feature\Modules\Category;

use App\Modules\Category\Models\Category;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CategoryCrudTest extends TestCase
{
    use RefreshDatabase;

    public function test_can_create_category(): void
    {
        $payload = [
            'name' => 'Test Name',
            'slug' => 'Test slug',
            'description' => 'Test description',
            'is_active' => true,
        ];
        $response = $this->postJson('/api/v1/categorys', $payload);
        $response->assertStatus(200)
            ->assertJsonStructure(['data' => ['id', 'created_at', 'updated_at']]);
        $this->assertDatabaseHas('categories', $payload);
    }

    public function test_can_list_categorys(): void
    {
        Category::factory()->count(2)->create();
        $response = $this->getJson('/api/v1/categorys');
        $response->assertStatus(200)
            ->assertJsonStructure(['data' => [['id', 'created_at', 'updated_at']]]);
    }

    public function test_can_show_category(): void
    {
        $category = Category::factory()->create();
        $response = $this->getJson("/api/v1/categorys/{$category->id}");
        $response->assertStatus(200)
            ->assertJsonStructure(['data' => ['id', 'created_at', 'updated_at']]);
    }

    public function test_can_update_category(): void
    {
        $category = Category::factory()->create();
        $payload = [
            'name' => 'Updated Name',
            'slug' => 'Updated slug',
            'description' => 'Updated description',
            'is_active' => false,
        ];
        $response = $this->putJson("/api/v1/categorys/{$category->id}", $payload);
        $response->assertStatus(200)
            ->assertJsonStructure(['data' => ['id', 'created_at', 'updated_at']]);
        $this->assertDatabaseHas('categories', array_merge(['id' => $category->id], $payload));
    }

    public function test_can_delete_category(): void
    {
        $category = Category::factory()->create();
        $response = $this->deleteJson("/api/v1/categorys/{$category->id}");
        $response->assertStatus(200)
            ->assertJson(['message' => 'Category deleted']);
        $this->assertDatabaseMissing('categories', ['id' => $category->id]);
    }
}
