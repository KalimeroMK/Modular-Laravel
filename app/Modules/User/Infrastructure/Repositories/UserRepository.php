<?php

declare(strict_types=1);

namespace App\Modules\User\Infrastructure\Repositories;

use App\Modules\Core\Repositories\EloquentRepository;
use App\Modules\User\Infrastructure\Models\User;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Cache;

class UserRepository extends EloquentRepository implements UserRepositoryInterface
{
    public function __construct(User $model)
    {
        parent::__construct($model);
    }

    public function findByEmail(string $email): ?User
    {
        $result = $this->findBy('email', $email);

        return $result instanceof User ? $result : null;
    }

    public function findByEmailWithRoles(string $email): ?User
    {
        $result = $this->findBy('email', $email, ['roles', 'permissions']);

        return $result instanceof User ? $result : null;
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

        return Cache::remember($cacheKey, $ttl, function () use ($perPage): LengthAwarePaginator {
            return $this->paginate($perPage);
        });
    }
}
