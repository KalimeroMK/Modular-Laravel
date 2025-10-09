<?php

declare(strict_types=1);

namespace Tests\Feature\Modules\TestComment;

use App\Modules\TestComment\Models\TestComment;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TestCommentCrudTest extends TestCase
{
    use RefreshDatabase;

    public function test_can_create_testcomment(): void
    {
        $payload = [
            'content' => 'Test content',
            'commentable' => 'Test commentable',
        ];
        $response = $this->postJson('/api/v1/testcomments', $payload);
        $response->assertStatus(200)
            ->assertJsonStructure(['data' => ['id', 'created_at', 'updated_at']]);
        $this->assertDatabaseHas('test_comments', $payload);
    }

    public function test_can_list_testcomments(): void
    {
        TestComment::factory()->count(2)->create();
        $response = $this->getJson('/api/v1/testcomments');
        $response->assertStatus(200)
            ->assertJsonStructure(['data' => [['id', 'created_at', 'updated_at']]]);
    }

    public function test_can_show_testcomment(): void
    {
        $testcomment = TestComment::factory()->create();
        $response = $this->getJson("/api/v1/testcomments/{$testcomment->id}");
        $response->assertStatus(200)
            ->assertJsonStructure(['data' => ['id', 'created_at', 'updated_at']]);
    }

    public function test_can_update_testcomment(): void
    {
        $testcomment = TestComment::factory()->create();
        $payload = [
            'content' => 'Updated content',
            'commentable' => 'Updated commentable',
        ];
        $response = $this->putJson("/api/v1/testcomments/{$testcomment->id}", $payload);
        $response->assertStatus(200)
            ->assertJsonStructure(['data' => ['id', 'created_at', 'updated_at']]);
        $this->assertDatabaseHas('test_comments', array_merge(['id' => $testcomment->id], $payload));
    }

    public function test_can_delete_testcomment(): void
    {
        $testcomment = TestComment::factory()->create();
        $response = $this->deleteJson("/api/v1/testcomments/{$testcomment->id}");
        $response->assertStatus(200)
            ->assertJson(['message' => 'TestComment deleted']);
        $this->assertDatabaseMissing('test_comments', ['id' => $testcomment->id]);
    }
}
