<?php

declare(strict_types=1);

namespace App\Modules\Core\Application\Actions;

use App\Modules\Core\Interfaces\RepositoryInterface;
use Illuminate\Database\Eloquent\Model;

abstract class AbstractDeleteAction
{
    public function __construct(protected RepositoryInterface $repository) {}

    public function execute(int|string $id): bool
    {
        $entity = $this->repository->findOrFail($id);

        $this->beforeDelete($entity, $id);

        return $this->repository->delete($id);
    }

    protected function beforeDelete(Model $entity, int|string $id): void {}
}
