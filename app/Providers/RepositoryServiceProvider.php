<?php

namespace App\Providers;

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
        \App\Modules\Auth\Interfaces\AuthInterface::class => \App\Modules\Auth\Repositories\AuthRepository::class,
        \App\Modules\Role\Interfaces\RoleInterface::class => \App\Modules\Role\Repositories\RoleRepository::class,
        \App\Modules\Permmisions\Interfaces\PermmisionsInterface::class => \App\Modules\Permmisions\Repositories\PermmisionsRepository::class,
        \App\Modules\Permission\Interfaces\PermissionInterface::class => \App\Modules\Permission\Repositories\PermissionRepository::class,
];

    /**
     * Register services.
     *
     * @return void
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
