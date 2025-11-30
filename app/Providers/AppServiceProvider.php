<?php

declare(strict_types=1);

namespace App\Providers;

use App\Modules\Core\Support\Generators\ModuleGenerationTracker;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        // Register ModuleGenerationTracker as singleton for tracking generated files
        $this->app->singleton(ModuleGenerationTracker::class, function ($app) {
            return new ModuleGenerationTracker($app->make(Filesystem::class));
        });
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void {}
}
