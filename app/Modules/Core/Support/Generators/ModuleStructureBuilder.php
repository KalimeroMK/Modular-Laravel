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
            'Http/DTOs',
            'Http/Actions',
            'Http/Controllers',
            'Http/Requests',
            'Http/Resources',
            'Models',
            'Repositories',
            'Interfaces',
            'database/migrations',
            'database/factories',
            'routes',
            'Config',
            'Exceptions',
            'Helpers',
            'Traits',
            'Observers',
            'Policies',
        ];

        foreach ($directories as $dir) {
            $path = $basePath.'/'.$dir;
            if (! $this->files->exists($path)) {
                $this->files->makeDirectory($path, 0755, true);
            }
        }
    }
}
