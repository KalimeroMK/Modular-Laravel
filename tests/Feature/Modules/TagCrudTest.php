<?php

namespace Tests\Feature\Modules\Tag;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Modules\Tag\Models\Tag;

class TagCrudTest extends TestCase
{
    use RefreshDatabase;

    public function test_index_returns_ok()
    {
        $response = $this->getJson('/api/v1/tags');
        $response->assertOk();
    }

    public function test_store_creates_resource()
    {
        
        $data = array (
  '' => 'test',
);
        $response = $this->postJson('/api/v1/tags', $data);
        $response->assertCreated();
    }

    public function test_show_returns_resource()
    {
        $model = Tag::factory()->create();
        $response = $this->getJson("/api/v1/tags/{$model->id}");
        $response->assertOk();
    }

    public function test_update_modifies_resource()
    {
        $model = Tag::factory()->create();
        
        $data = array (
  '' => 'updated',
);
        $response = $this->putJson("/api/v1/tags/{$model->id}", $data);
        $response->assertOk();
    }

    public function test_destroy_deletes_resource()
    {
        $model = Tag::factory()->create();
        $response = $this->deleteJson("/api/v1/tags/{$model->id}");
        $response->assertNoContent();
    }
}
