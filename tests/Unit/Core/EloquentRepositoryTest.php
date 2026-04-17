<?php

declare(strict_types=1);

namespace Tests\Unit\Core;

use App\Modules\User\Infrastructure\Models\User;
use App\Modules\User\Infrastructure\Repositories\UserRepository;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class EloquentRepositoryTest extends TestCase
{
    use RefreshDatabase;

    protected UserRepository $repository;

    protected function setUp(): void
    {
        parent::setUp();
        $this->repository = new UserRepository(new User());
    }

    public function test_all_returns_collection(): void
    {

        User::factory()->count(5)->create();

        $result = $this->repository->all();

        $this->assertCount(5, $result);
        $this->assertInstanceOf(\Illuminate\Support\Collection::class, $result);
    }

    public function test_find_returns_user(): void
    {

        $user = User::factory()->create();

        $result = $this->repository->find($user->id);

        $this->assertInstanceOf(User::class, $result);
        $this->assertEquals($user->id, $result->id);
    }

    public function test_find_returns_null_when_not_found(): void
    {

        $result = $this->repository->find(999);

        $this->assertNull($result);
    }

    public function test_find_or_fail_returns_user(): void
    {

        $user = User::factory()->create();

        $result = $this->repository->findOrFail($user->id);

        $this->assertInstanceOf(User::class, $result);
        $this->assertEquals($user->id, $result->id);
    }

    public function test_find_or_fail_throws_exception_when_not_found(): void
    {

        $this->expectException(\Illuminate\Database\Eloquent\ModelNotFoundException::class);
        $this->repository->findOrFail(999);
    }

    public function test_find_by_returns_user(): void
    {

        $user = User::factory()->create([
            'email' => 'test@example.com',
        ]);

        $result = $this->repository->findBy('email', 'test@example.com');

        $this->assertInstanceOf(User::class, $result);
        $this->assertEquals($user->id, $result->id);
    }

    public function test_find_by_returns_null_when_not_found(): void
    {

        $result = $this->repository->findBy('email', 'nonexistent@example.com');

        $this->assertNull($result);
    }

    public function test_create_returns_user(): void
    {

        $userData = [
            'name' => 'Test User',
            'email' => 'test@example.com',
            'password' => bcrypt('password123'),
        ];

        $result = $this->repository->create($userData);

        $this->assertInstanceOf(User::class, $result);
        $this->assertEquals('Test User', $result->name);
        $this->assertEquals('test@example.com', $result->email);
    }

    public function test_update_returns_updated_user(): void
    {

        $user = User::factory()->create();
        $updateData = [
            'name' => 'Updated Name',
        ];

        $result = $this->repository->update($user->id, $updateData);

        $this->assertInstanceOf(User::class, $result);
        $this->assertEquals('Updated Name', $result->name);
    }

    public function test_delete_returns_true(): void
    {

        $user = User::factory()->create();

        $result = $this->repository->delete($user->id);

        $this->assertTrue($result);
        $this->assertDatabaseMissing('users', [
            'id' => $user->id,
        ]);
    }

    public function test_insert_returns_true(): void
    {

        $usersData = [
            [
                'name' => 'User 1',
                'email' => 'user1@example.com',
                'password' => bcrypt('password123'),
            ],
            [
                'name' => 'User 2',
                'email' => 'user2@example.com',
                'password' => bcrypt('password123'),
            ],
        ];

        $result = $this->repository->insert($usersData);

        $this->assertTrue($result);
        $this->assertDatabaseHas('users', [
            'email' => 'user1@example.com',
        ]);
        $this->assertDatabaseHas('users', [
            'email' => 'user2@example.com',
        ]);
    }

    public function test_all_cached_is_invalidated_after_create(): void
    {
        $this->repository->create([
            'name' => 'User 1',
            'email' => 'user1@example.com',
            'password' => bcrypt('password123'),
        ]);
        $cached = $this->repository->allCached();
        $this->assertCount(1, $cached);

        $this->repository->create([
            'name' => 'User 2',
            'email' => 'user2@example.com',
            'password' => bcrypt('password123'),
        ]);
        $fresh = $this->repository->allCached();
        $this->assertCount(2, $fresh);
    }

    public function test_all_cached_with_relations_is_invalidated_after_create(): void
    {
        $this->repository->create([
            'name' => 'User 1',
            'email' => 'user3@example.com',
            'password' => bcrypt('password123'),
        ]);
        $cached = $this->repository->allCached(['roles']);
        $this->assertCount(1, $cached);

        $this->repository->create([
            'name' => 'User 2',
            'email' => 'user4@example.com',
            'password' => bcrypt('password123'),
        ]);
        $fresh = $this->repository->allCached(['roles']);
        $this->assertCount(2, $fresh);
    }

    public function test_find_cached_is_invalidated_after_update(): void
    {
        $user = User::factory()->create(['name' => 'Original']);
        $cached = $this->repository->findCached($user->id);
        $this->assertEquals('Original', $cached->name);

        $this->repository->update($user->id, ['name' => 'Updated']);
        $fresh = $this->repository->findCached($user->id);
        $this->assertEquals('Updated', $fresh->name);
    }

    public function test_find_cached_with_relations_is_invalidated_after_update(): void
    {
        $user = User::factory()->create(['name' => 'Original']);
        $cached = $this->repository->findCached($user->id, ['roles']);
        $this->assertEquals('Original', $cached->name);

        $this->repository->update($user->id, ['name' => 'Updated']);
        $fresh = $this->repository->findCached($user->id, ['roles']);
        $this->assertEquals('Updated', $fresh->name);
    }

    public function test_find_cached_is_invalidated_after_delete(): void
    {
        $user = User::factory()->create();
        $cached = $this->repository->findCached($user->id);
        $this->assertNotNull($cached);

        $this->repository->delete($user->id);
        $fresh = $this->repository->findCached($user->id);
        $this->assertNull($fresh);
    }

    public function test_clear_cache_clears_all_cached_entries(): void
    {
        $user = User::factory()->create();
        $this->repository->findCached($user->id);
        $this->repository->findCached($user->id, ['roles']);
        $this->repository->allCached();
        $this->repository->allCached(['roles']);

        $this->repository->clearCache();

        $reflection = new \ReflectionMethod($this->repository, 'getCacheKey');
        $reflection->setAccessible(true);

        $keyAll = $reflection->invoke($this->repository, 'all');
        $keyAllWith = $reflection->invoke($this->repository, 'all', ['roles']);
        $keyFind = $reflection->invoke($this->repository, 'find', [], $user->id);
        $keyFindWith = $reflection->invoke($this->repository, 'find', ['roles'], $user->id);

        $this->assertNull(\Illuminate\Support\Facades\Cache::get($keyAll));
        $this->assertNull(\Illuminate\Support\Facades\Cache::get($keyAllWith));
        $this->assertNull(\Illuminate\Support\Facades\Cache::get($keyFind));
        $this->assertNull(\Illuminate\Support\Facades\Cache::get($keyFindWith));
    }
}
