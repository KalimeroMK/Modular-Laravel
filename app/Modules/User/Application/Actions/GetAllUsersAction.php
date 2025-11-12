<?php

declare(strict_types=1);

namespace App\Modules\User\Application\Actions;

use App\Modules\User\Infrastructure\Repositories\UserRepositoryInterface;
use Illuminate\Pagination\LengthAwarePaginator;

class GetAllUsersAction
{
    public function __construct(
        protected UserRepositoryInterface $userRepository,
    ) {}

    /**
     * @return LengthAwarePaginator<int, \App\Modules\User\Infrastructure\Models\User>
     */
    public function execute(int $perPage = 15): LengthAwarePaginator
    {
        /** @var LengthAwarePaginator<int, \App\Modules\User\Infrastructure\Models\User> $result */
        $result = $this->userRepository->paginate($perPage);

        return $result;
    }
}
