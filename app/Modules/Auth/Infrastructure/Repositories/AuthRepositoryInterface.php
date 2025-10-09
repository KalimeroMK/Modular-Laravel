<?php

declare(strict_types=1);

namespace App\Modules\Auth\Infrastructure\Repositories;

use App\Modules\Core\Interfaces\RepositoryInterface;
use App\Modules\User\Infrastructure\Models\User;

interface AuthRepositoryInterface extends RepositoryInterface
{
    public function findByEmail(string $email): ?User;
}
