<?php

declare(strict_types=1);

namespace App\Modules\Core\Support\Generators;

use Illuminate\Filesystem\Filesystem;

class ModuleStructureBuilder
{
    public function __construct(protected Filesystem $files) {}

    public function create(string $moduleName): void
    {
        $basePath = app_path("Modules/{$moduleName}");

        $directories = [
            // Application Layer
            'Application/Actions',
            'Application/DTO',
            'Application/Services',
            'Application/Events',
            'Application/Exceptions',
            'Application/Listeners',

            // Infrastructure Layer
            'Infrastructure/Http/Controllers',
            'Infrastructure/Http/Requests',
            'Infrastructure/Http/Middleware',
            'Infrastructure/Models',
            'Infrastructure/Repositories',
            'Infrastructure/Policies',
            'Infrastructure/Providers',
            'Infrastructure/Routes',

            // Database Layer
            'Database/Migrations',
            'Database/Factories',
        ];

        foreach ($directories as $dir) {
            $path = $basePath.'/'.$dir;
            if (! $this->files->exists($path)) {
                $this->files->makeDirectory($path, 0755, true);
            }
        }
    }
}
