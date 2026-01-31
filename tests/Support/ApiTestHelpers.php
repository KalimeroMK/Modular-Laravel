<?php

declare(strict_types=1);

namespace Tests\Support;

use App\Modules\User\Infrastructure\Models\User;
use Illuminate\Testing\TestResponse;
use Laravel\Sanctum\Sanctum;




trait ApiTestHelpers
{
    


    protected function createAuthenticatedUser(array $attributes = []): User
    {
        $user = User::factory()->create($attributes);
        Sanctum::actingAs($user);

        return $user;
    }

    







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

    


    protected function createToken(User $user, string $tokenName = 'test-token'): string
    {
        return $user->createToken($tokenName)->plainTextToken;
    }

    


    protected function actingAsUser(User $user): void
    {
        Sanctum::actingAs($user);
    }

    




    protected function actingAsUserWithAbilities(User $user, array $abilities = ['*']): void
    {
        Sanctum::actingAs($user, $abilities);
    }
}
