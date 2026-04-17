<?php

declare(strict_types=1);

namespace App\Modules\Core\Application\Actions;

use App\Modules\Core\Application\Contracts\DtoInterface;
use App\Modules\Core\Exceptions\UpdateException;
use App\Modules\Core\Interfaces\RepositoryInterface;
use Illuminate\Database\Eloquent\Model;

abstract class AbstractUpdateAction
{
    public function __construct(protected RepositoryInterface $repository) {}

    final public function execute(int|string $id, DtoInterface $dto): Model
    {
        $this->repository->findOrFail($id);

        $data = $this->beforeUpdate(
            array_filter($this->mapDtoToArray($dto), fn ($value) => $value !== null)
        );

        $result = $this->repository->update($id, $data);

        if ($result === null) {
            throw new UpdateException('Failed to update '.$this->getEntityName());
        }

        $this->afterUpdate($result);

        return $result;
    }

    protected function mapDtoToArray(DtoInterface $dto): array
    {
        return $dto->toArray();
    }

    protected function beforeUpdate(array $data): array
    {
        return $data;
    }

    protected function afterUpdate(Model $model): void {}

    protected function getEntityName(): string
    {
        $class = basename(str_replace('\\', '/', static::class));

        return mb_strtolower(str_replace(['Update', 'Action'], '', $class));
    }
}
