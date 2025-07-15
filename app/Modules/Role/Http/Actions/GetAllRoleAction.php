<?php

declare(strict_types=1);

namespace App\Modules\Role\Http\Actions;

use App\Modules\Role\Interfaces\RoleInterface;
use App\Modules\Role\Models\Role;
use Illuminate\Support\Collection;

class GetAllRoleAction
{
    public function __construct(protected RoleInterface $repository) {}

    /**
     * @return \Illuminate\Support\Collection<int, \App\Modules\Role\Models\Role>
     */
    public function execute(): Collection
    {
        /** @var Collection<int, Role> $result */
        $result = $this->repository->all();
        return $result;
    }
}
