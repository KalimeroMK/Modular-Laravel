<?php

namespace App\Providers;

use App\Modules\Auth\Interfaces\AuthInterface;
use App\Modules\Auth\Repositories\AuthRepository;
use App\Modules\User\Interfaces\UserInterface;
use App\Modules\User\Repositories\UserRepository;
use Illuminate\Support\ServiceProvider;

class RepositoryServiceProvider extends ServiceProvider
{
    /**
     * @var string[]
     */
    protected array $repositories = [

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
