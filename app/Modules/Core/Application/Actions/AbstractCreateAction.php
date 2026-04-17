<?php

declare(strict_types=1);

namespace App\Modules\Core\Application\Actions;

use App\Modules\Core\Application\Contracts\DtoInterface;
use App\Modules\Core\Exceptions\CreateException;
use App\Modules\Core\Interfaces\RepositoryInterface;
use Illuminate\Database\Eloquent\Model;

abstract class AbstractCreateAction
{
    public function __construct(protected RepositoryInterface $repository) {}

    final public function execute(DtoInterface $dto): Model
    {
        $data = $this->beforeCreate($this->mapDtoToArray($dto));

        $result = $this->repository->create($data);

        if ($result === null) {
            throw new CreateException('Failed to create '.$this->getEntityName());
        }

        $this->afterCreate($result);

        return $result;
    }

    protected function mapDtoToArray(DtoInterface $dto): array
    {
        return $dto->toArray();
    }

    protected function beforeCreate(array $data): array
    {
        return $data;
    }

    protected function afterCreate(Model $model): void {}

    protected function getEntityName(): string
    {
        $class = basename(str_replace('\\', '/', static::class));

        return mb_strtolower(str_replace(['Create', 'Action'], '', $class));
    }
}
