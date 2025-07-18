<?php

declare(strict_types=1);

namespace Tests\Feature\Modules\TestComment;

use App\Modules\TestComment\Models\TestComment;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TestCommentCrudTest extends TestCase
{
    use RefreshDatabase;

    public function test_can_list_testcomments(): void
    {
        TestComment::factory()->count(2)->create();
        $response = $this->getJson('/api/v1/testcomments');
        $response->assertStatus(200)
            ->assertJsonStructure(['data' => [['id']]]);
    }

    public function test_can_create_testcomment(): void
    {
        $related = $this->createRelatedModels();

        $data = array_merge([
                        'content' => 'Test content',
            'commentable' => 'Test commentable',
        ], $related);

        $response = $this->postJson('/api/v1/testcomments', $data);
        $response->assertStatus(201)
            ->assertJsonStructure(['data' => ['id']]);
        
        // Verify in database
        $responseData = $response->json('data');
        $this->assertDatabaseHas('test_comments', ['id' => $responseData['id']]);
    }

    public function test_can_show_testcomment(): void
    {
        $model = TestComment::factory()->create();
        $response = $this->getJson("/api/v1/testcomments/{$model->id}");
        $response->assertStatus(200)
            ->assertJsonStructure(['data' => ['id']])
            ->assertJson(['data' => ['id' => $model->id]]);
    }

    public function test_can_update_testcomment(): void
    {
        $model = TestComment::factory()->create();

        $data = array_merge([
                        'content' => 'Updated content',
            'commentable' => 'Updated commentable',
        ], $this->createRelatedModels());

        $response = $this->putJson("/api/v1/testcomments/{$model->id}", $data);
        $response->assertStatus(200)
            ->assertJsonStructure(['data' => ['id']])
            ->assertJson(['data' => ['id' => $model->id]]);
        
        // Verify changes in database
        $this->assertDatabaseHas('test_comments', array_merge(['id' => $model->id], $data));
    }

    public function test_can_delete_testcomment(): void
    {
        $model = TestComment::factory()->create();
        $response = $this->deleteJson("/api/v1/testcomments/{$model->id}");
        $response->assertStatus(200)
            ->assertJsonStructure(['message']);
        
        // Verify deletion in database
        $this->assertDatabaseMissing('test_comments', ['id' => $model->id]);
    }

    public function test_show_testcomment_not_found(): void
    {
        $response = $this->getJson('/api/v1/testcomments/999999');
        $response->assertStatus(404);
    }

    public function test_update_testcomment_not_found(): void
    {
        $data = array_merge([
                        'content' => 'Updated content',
            'commentable' => 'Updated commentable',
        ], $this->createRelatedModels());

        $response = $this->putJson('/api/v1/testcomments/999999', $data);
        $response->assertStatus(404);
    }

    public function test_delete_testcomment_not_found(): void
    {
        $response = $this->deleteJson('/api/v1/testcomments/999999');
        $response->assertStatus(404);
    }

    protected function createRelatedModels(): array
    {
        return [];
    }
}
