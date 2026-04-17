<?php

declare(strict_types=1);

namespace App\Modules\Core\Application\Actions;

use App\Modules\Core\Interfaces\RepositoryInterface;
use Illuminate\Database\Eloquent\Model;

abstract class AbstractGetByIdAction
{
    public function __construct(protected RepositoryInterface $repository) {}

    public function execute(int|string $id, array $with = []): Model
    {
        return $with !== []
            ? $this->repository->findOrFail($id, $with)
            : $this->repository->findOrFail($id);
    }
}
