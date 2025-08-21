<?php

declare(strict_types=1);

namespace App\Modules\Permission\Http\Actions;

use App\Modules\Permission\Interfaces\PermissionInterface;
use App\Modules\Permission\Models\Permission;
use Illuminate\Support\Collection;

class GetAllPermissionAction
{
    public function __construct(protected PermissionInterface $repository) {}

    /**
     * @return Collection<int, Permission>
     */
    public function execute(): Collection
    {
        /** @var Collection<int, Permission> $result */
        $result = $this->repository->all();

        return $result;
    }
}
