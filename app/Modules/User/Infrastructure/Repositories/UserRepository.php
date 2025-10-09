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
        /** @var User|null $result */
        $result = $this->findBy('email', $email);

        return $result;
    }

    /**
     * Find user by email with roles and permissions eager loaded
     */
    public function findByEmailWithRoles(string $email): ?User
    {
        /** @var User|null $result */
        $result = $this->findBy('email', $email, ['roles', 'permissions']);

        return $result;
    }

    /**
     * Get users with their roles and permissions
     * 
     * @return LengthAwarePaginator<int, User>
     */
    public function paginateWithRoles(int $perPage = 15): LengthAwarePaginator
    {
        /** @var LengthAwarePaginator<int, User> $result */
        $result = $this->query()
            ->with(['roles', 'permissions'])
            ->paginate($perPage);

        return $result;
    }

    /**
     * Get cached users list
     * 
     * @return LengthAwarePaginator<int, User>
     */
    public function paginateCached(int $perPage = 15, int $ttl = 1800): LengthAwarePaginator
    {
        $cacheKey = "users_paginated_{$perPage}";
        
        return \Illuminate\Support\Facades\Cache::remember($cacheKey, $ttl, function () use ($perPage) {
            return $this->paginate($perPage);
        });
    }
}
