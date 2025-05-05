<?php

namespace App\Providers;

use App\Modules\Auth\Interfaces\AuthInterface;
use App\Modules\Auth\Repositories\AuthRepository;
use App\Modules\Permission\Interfaces\PermissionInterface;
use App\Modules\Permission\Repositories\PermissionRepository;
use App\Modules\Role\Interfaces\RoleInterface;
use App\Modules\Role\Repositories\RoleRepository;
use App\Modules\User\Interfaces\UserInterface;
use App\Modules\User\Repositories\UserRepository;
use Illuminate\Support\ServiceProvider;

class RepositoryServiceProvider extends ServiceProvider
{
    /**
     * @var string[]
     */
    protected array $repositories = [
        UserInterface::class => UserRepository::class,
        AuthInterface::class => AuthRepository::class,
        RoleInterface::class => RoleRepository::class,
        PermissionInterface::class => PermissionRepository::class,
    ];

    /**
     * Register services.
     */
    public function register(): void
    {
        foreach ($this->repositories as $interface => $repository) {
            $this->app->bind($interface, function ($app) use ($repository) {
                return new $repository;
            });
        }
    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        //
    }
}
