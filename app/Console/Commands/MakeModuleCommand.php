<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Contracts\Filesystem\FileNotFoundException;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\Str;

class MakeModuleCommand extends Command
{
    protected $signature = 'make:module {name : The name of the module} {--api : Generate an API controller and routes}';

    protected $description = 'Create a new module with predefined structure and files';

    protected Filesystem $files;

    public function __construct(Filesystem $files)
    {
        parent::__construct();
        $this->files = $files;
    }

    public function handle(): int
    {
        $moduleName = Str::studly($this->argument('name'));
        $modulePath = app_path("Modules/{$moduleName}");
        $isApi = $this->option('api');

        if ($this->files->exists($modulePath)) {
            $this->error("Module '{$moduleName}' already exists!");
            return 1;
        }

        try {
            $this->createModuleStructure($modulePath, $moduleName, $isApi);
            $this->info("Module '{$moduleName}' created successfully!");
        } catch (\Exception $e) {
            $this->cleanupModule($modulePath);
            $this->error("Failed to create module: {$e->getMessage()}");
            return 1;
        }

        return 0;
    }

    /**
     * @throws FileNotFoundException
     */
    protected function createModuleStructure(string $modulePath, string $moduleName, bool $isApi): void
    {
        $structure = [
            'Config',
            'Http/Controllers',
            'Http/Controllers/Api',
            'Http/Requests',
            'Exceptions',
            'Filters',
            'Helpers',
            'Interfaces',
            'Models',
            'Repositories',
            'Resources/lang',
            'Resources/views/layouts',
            'Resources/views',
            'routes',
            'Services',
            'Traits',
            'Http/Transformers',
            'database/migrations',
            'database/factories',
        ];

        foreach ($structure as $directory) {
            $path = "{$modulePath}/{$directory}";
            if (!$this->files->exists($path)) {
                $this->files->makeDirectory($path, 0755, true);
            }
        }

        $this->generateFiles($modulePath, $moduleName, $isApi);
    }

    /**
     * @throws FileNotFoundException
     */
    protected function generateFiles(string $modulePath, string $moduleName, bool $isApi): void
    {
        $tableName = Str::plural(Str::snake($moduleName)); // Convert module name to table name

        $stubs = [
            "Http/Controllers/" . ($isApi ? "Api/" : "") . "{{module}}Controller.php" =>
                base_path('stubs/module/Controllers/' . ($isApi ? 'ApiController.stub' : 'Controller.stub')),
            'Interfaces/{{module}}Interface.php' =>
                base_path('stubs/module/Interface.stub'),
            'Repositories/{{module}}Repository.php' =>
                base_path('stubs/module/Repository.stub'),
            'Models/{{module}}.php' =>
                base_path('stubs/module/Model.stub'),
            'routes/' . ($isApi ? 'api.php' : 'web.php') =>
                base_path('stubs/module/routes/' . ($isApi ? 'api.stub' : 'web.stub')),
            'database/migrations/{{timestamp}}_create_{{table}}_table.php' =>
                base_path('stubs/module/Migration.stub'),
            'database/factories/{{module}}Factory.php' =>
                base_path('stubs/module/Factory.stub'),
            'Http/Transformers/{{module}}Resource.php' =>
                base_path('stubs/module/Resource.stub'),
            'Services/{{module}}Service.php' =>
                base_path('stubs/module/Service.stub'),
            'Resources/views/index.blade.php' =>
                base_path('stubs/module/Resources/index.blade.stub'),
            'Resources/views/create.blade.php' =>
                base_path('stubs/module/Resources/create.blade.stub'),
            'Resources/views/show.blade.php' =>
                base_path('stubs/module/Resources/show.blade.stub'),
            'Resources/views/layouts/master.blade.php' =>
                base_path('stubs/module/Resources/master.blade.stub'),
        ];

        // Map exception types to their specific stubs
        $exceptionStubs = [
            "Exceptions/{{module}}DestroyException.php" => base_path('stubs/module/Exceptions/DestroyException.stub'),
            "Exceptions/{{module}}IndexException.php"   => base_path('stubs/module/Exceptions/IndexException.stub'),
            "Exceptions/{{module}}NotFoundException.php" => base_path('stubs/module/Exceptions/NotFoundException.stub'),
            "Exceptions/{{module}}SearchException.php"  => base_path('stubs/module/Exceptions/SearchException.stub'),
            "Exceptions/{{module}}StoreException.php"   => base_path('stubs/module/Exceptions/StoreException.stub'),
            "Exceptions/{{module}}UpdateException.php"  => base_path('stubs/module/Exceptions/UpdateException.stub'),
        ];

        // Map request types to their specific stubs
        $requestStubs = [
            "Http/Requests/Create{$moduleName}Request.php" => base_path('stubs/module/Request/CreateRequest.stub'),
            "Http/Requests/Delete{$moduleName}Request.php" => base_path('stubs/module/Request/DeleteRequest.stub'),
            "Http/Requests/Search{$moduleName}Request.php" => base_path('stubs/module/Request/SearchRequest.stub'),
            "Http/Requests/Show{$moduleName}Request.php"   => base_path('stubs/module/Request/ShowRequest.stub'),
            "Http/Requests/Update{$moduleName}Request.php" => base_path('stubs/module/Request/UpdateRequest.stub'),
        ];

        // Map filter types to their specific stubs
        $filterStubs = [
            "Filters/IsActive{{module}}.php" => base_path('stubs/module/Filters/IsActive.stub'),
            "Filters/Name{{module}}.php" => base_path('stubs/module/Filters/Name.stub'),
            "Filters/Type{{module}}.php" => base_path('stubs/module/Filters/Type.stub'),
            "Filters/TypeId{{module}}.php" => base_path('stubs/module/Filters/TypeId.stub'),
        ];

        // Merge all stubs into the main stubs array
        $stubs = array_merge($stubs, $exceptionStubs, $requestStubs, $filterStubs);

        foreach ($stubs as $file => $stubPath) {
            if ($this->files->exists($stubPath)) {
                $stubContent = $this->files->get($stubPath);

                $content = str_replace(
                    ['{{module}}', '{{module_lower}}', '{{table}}', '{{timestamp}}'],
                    [$moduleName, Str::lower($moduleName), $tableName, now()->format('Y_m_d_His')],
                    $stubContent
                );

                $filePath = "{$modulePath}/" . str_replace(
                        ['{{module}}', '{{table}}', '{{timestamp}}'],
                        [$moduleName, $tableName, now()->format('Y_m_d_His')],
                        $file
                    );

                $this->files->put($filePath, $content);
            } else {
                $this->error("Stub file not found: {$stubPath}");
            }
        }

        $this->info("Files generated for module '{$moduleName}'.");
    }

    protected function cleanupModule(string $modulePath): void
    {
        if ($this->files->exists($modulePath)) {
            $this->files->deleteDirectory($modulePath);
            $this->info("Cleaned up incomplete module at {$modulePath}.");
        }
    }
}
