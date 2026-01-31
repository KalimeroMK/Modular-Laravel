<?php

declare(strict_types=1);

namespace App\Modules\User\Infrastructure\Repositories;

use App\Modules\Core\Interfaces\RepositoryInterface;
use App\Modules\User\Infrastructure\Models\User;
use Illuminate\Pagination\LengthAwarePaginator;

interface UserRepositoryInterface extends RepositoryInterface
{
    public function findByEmail(string $email): ?User;

    


    public function findByEmailWithRoles(string $email): ?User;

    




    public function paginateWithRoles(int $perPage = 15): LengthAwarePaginator;

    




    public function paginateCached(int $perPage = 15, int $ttl = 1800): LengthAwarePaginator;
}
