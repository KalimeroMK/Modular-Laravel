<?php

declare(strict_types=1);

namespace App\Modules\User\Infrastructure\Repositories;

use App\Modules\Core\Repositories\EloquentRepository;
use App\Modules\User\Infrastructure\Models\User;
use Illuminate\Pagination\LengthAwarePaginator;

class UserRepository extends EloquentRepository implements UserRepositoryInterface
{
    public function __construct(User $model)
    {
        parent::__construct($model);
    }

    public function findByEmail(string $email): ?User
    {

        $result = $this->findBy('email', $email);

        return $result;
    }

    public function findByEmailWithRoles(string $email): ?User
    {

        $result = $this->findBy('email', $email, ['roles', 'permissions']);

        return $result;
    }

    public function paginateWithRoles(int $perPage = 15): LengthAwarePaginator
    {

        $result = $this->query()
            ->with(['roles', 'permissions'])
            ->paginate($perPage);

        return $result;
    }

    public function paginateCached(int $perPage = 15, int $ttl = 1800): LengthAwarePaginator
    {
        $cacheKey = "users_paginated_{$perPage}";

        return \Illuminate\Support\Facades\Cache::remember($cacheKey, $ttl, fn () => $this->paginate($perPage));
    }
}
