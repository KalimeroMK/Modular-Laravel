<?php

declare(strict_types=1);

namespace App\Modules\Permission\Application\Actions;

use App\Modules\Core\Application\Actions\AbstractDeleteAction;
use App\Modules\Permission\Infrastructure\Repositories\PermissionRepositoryInterface;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class DeletePermissionAction extends AbstractDeleteAction
{
    public function __construct(PermissionRepositoryInterface $repository)
    {
        parent::__construct($repository);
    }

    protected function beforeDelete(Model $entity, int|string $id): void
    {
        DB::table('role_has_permissions')->where('permission_id', $id)->delete();
        DB::table('model_has_permissions')->where('permission_id', $id)->delete();
    }
}
