<?php

declare(strict_types=1);

namespace App\Modules\User\Http\Actions;

use App\Modules\User\Interfaces\UserInterface;
use App\Modules\User\Models\User;
use Illuminate\Support\Collection;

class GetAllUserAction
{
    public function __construct(protected UserInterface $repository) {}

    /**
     * @return Collection<int, User>
     */
    public function execute(): Collection
    {
        return $this->repository->all();
    }
}
