<?php

namespace App\Providers;

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
