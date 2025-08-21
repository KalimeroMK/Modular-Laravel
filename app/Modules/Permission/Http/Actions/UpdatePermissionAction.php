<?php

declare(strict_types=1);

namespace App\Modules\Permission\Http\Actions;

use App\Modules\Permission\Http\DTOs\PermissionDTO;
use App\Modules\Permission\Interfaces\PermissionInterface;
use Illuminate\Database\Eloquent\Model;

class UpdatePermissionAction
{
    public function __construct(protected PermissionInterface $repository) {}

    public function execute(PermissionDTO $dto): Model
    {
        return $this->repository->update((int) $dto->id, [
            'name' => $dto->name,
            'guard_name' => $dto->guard_name,
        ]);
    }
}
