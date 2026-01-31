<?php

declare(strict_types=1);

namespace App\Providers;

use App\Modules\Core\Support\Generators\ModuleConfigUpdater;
use App\Modules\Core\Support\Generators\ModuleGenerationTracker;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\ServiceProvider;
use Override;

class AppServiceProvider extends ServiceProvider
{
    


    
    public function register(): void
    {
        
        $this->app->singleton(ModuleGenerationTracker::class, fn ($app) => new ModuleGenerationTracker(
            $app->make(Filesystem::class),
            $app->make(ModuleConfigUpdater::class)
        ));
    }

    


    public function boot(): void
    {
        
        Model::preventLazyLoading(! $this->app->isProduction());

        
        Model::preventSilentlyDiscardingAttributes(! $this->app->isProduction());

        
        Model::preventAccessingMissingAttributes(! $this->app->isProduction());
    }
}
