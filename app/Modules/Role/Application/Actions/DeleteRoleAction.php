<?php

declare(strict_types=1);

namespace App\Modules\Role\Application\Actions;

use App\Modules\Core\Application\Actions\AbstractDeleteAction;
use App\Modules\Role\Infrastructure\Repositories\RoleRepositoryInterface;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class DeleteRoleAction extends AbstractDeleteAction
{
    public function __construct(RoleRepositoryInterface $repository)
    {
        parent::__construct($repository);
    }

    protected function beforeDelete(Model $entity, int|string $id): void
    {
        DB::table('role_has_permissions')->where('role_id', $id)->delete();
        DB::table('model_has_roles')->where('role_id', $id)->delete();
    }
}
