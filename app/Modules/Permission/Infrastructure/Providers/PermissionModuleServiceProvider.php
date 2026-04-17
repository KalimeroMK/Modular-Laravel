<?php

declare(strict_types=1);

namespace App\Modules\Permission\Infrastructure\Providers;

use App\Modules\Core\Infrastructure\Providers\AbstractCrudModuleServiceProvider;
use App\Modules\Permission\Infrastructure\Models\Permission;
use App\Modules\Permission\Infrastructure\Policies\PermissionPolicy;
use App\Modules\Permission\Infrastructure\Repositories\PermissionRepository;
use App\Modules\Permission\Infrastructure\Repositories\PermissionRepositoryInterface;

class PermissionModuleServiceProvider extends AbstractCrudModuleServiceProvider
{
    protected function getModuleConfig(): array
    {
        return [
            'moduleName' => 'Permission',
            'model' => Permission::class,
            'policy' => PermissionPolicy::class,
            'repository' => PermissionRepository::class,
            'interface' => PermissionRepositoryInterface::class,
            'routeFile' => __DIR__.'/../Routes/permissions.php',
        ];
    }
}
