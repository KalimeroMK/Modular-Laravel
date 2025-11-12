<?php

declare(strict_types=1);

namespace Tests\Support;

use App\Modules\User\Infrastructure\Models\User;
use Illuminate\Testing\TestResponse;
use Laravel\Sanctum\Sanctum;

/**
 * Trait with helper methods for API testing with authentication.
 */
trait ApiTestHelpers
{
    /**
     * Create an authenticated user and return it.
     */
    protected function createAuthenticatedUser(array $attributes = []): User
    {
        $user = User::factory()->create($attributes);
        Sanctum::actingAs($user);

        return $user;
    }

    /**
     * Make an authenticated JSON request.
     *
     * @param  string  $method  HTTP method
     * @param  string  $uri  URI
     * @param  array<string, mixed>  $data  Request data
     * @param  array<string, string>  $headers  Additional headers
     */
    protected function authenticatedJson(
        string $method,
        string $uri,
        array $data = [],
        array $headers = []
    ): TestResponse {
        $user = $this->createAuthenticatedUser();

        return $this->withHeaders(array_merge([
            'Authorization' => 'Bearer '.$this->createToken($user),
        ], $headers))->json($method, $uri, $data);
    }

    /**
     * Create a token for a user.
     */
    protected function createToken(User $user, string $tokenName = 'test-token'): string
    {
        return $user->createToken($tokenName)->plainTextToken;
    }

    /**
     * Act as a user for subsequent requests.
     */
    protected function actingAsUser(User $user): void
    {
        Sanctum::actingAs($user);
    }

    /**
     * Act as a user with specific abilities.
     *
     * @param  array<string>  $abilities
     */
    protected function actingAsUserWithAbilities(User $user, array $abilities = ['*']): void
    {
        Sanctum::actingAs($user, $abilities);
    }
}
