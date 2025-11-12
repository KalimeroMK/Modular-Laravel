<?php

declare(strict_types=1);

namespace App\Modules\Auth\Infrastructure\Repositories;

use App\Modules\Core\Repositories\EloquentRepository;
use App\Modules\User\Infrastructure\Models\User;

/**
 * @extends EloquentRepository<User>
 */
class AuthRepository extends EloquentRepository implements AuthRepositoryInterface
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
}
