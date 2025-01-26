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

    /**
     * @return int
     */
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
            $this->createModuleStructure($modulePath);
            $this->generateFiles($modulePath, $moduleName, $isApi);
            $this->updateRepositoryServiceProvider($moduleName);
            $this->info("Module '{$moduleName}' created successfully!");
        } catch (\Exception $e) {
            $this->cleanupModule($modulePath);
            $this->error("Failed to create module: {$e->getMessage()}");
            return 1;
        }

        return 0;
    }

    /**
     * @param  string  $modulePath
     * @return void
     */
    protected function createModuleStructure(string $modulePath): void
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
            'Http/Resources',
            'database/migrations',
            'database/factories',
        ];

        foreach ($structure as $directory) {
            $path = "$modulePath/$directory";
            if (!$this->files->exists($path)) {
                $this->files->makeDirectory($path, 0755, true);
            }
        }
    }

    /**
     * @param  string  $modulePath
     * @param  string  $moduleName
     * @param  bool    $isApi
     * @return void
     * @throws FileNotFoundException
     */
    protected function generateFiles(string $modulePath, string $moduleName, bool $isApi): void
    {
        $tableName = Str::plural(Str::snake($moduleName));
        $stubs = $this->getStubFiles($moduleName, $isApi);

        foreach ($stubs as $file => $stubPath) {
            if (!$this->files->exists($stubPath)) {
                $this->error("Stub file not found: {$stubPath}");
                continue;
            }

            $stubContent = $this->files->get($stubPath);
            $content = str_replace([
                '{{module}}', '{{module_lower}}', '{{table}}', '{{timestamp}}'
            ], [
                $moduleName, Str::lower($moduleName), $tableName, now()->format('Y_m_d_His')
            ], $stubContent);

            $filePath = "$modulePath/" . str_replace([
                    '{{module}}', '{{table}}', '{{timestamp}}'
                ], [
                    $moduleName, $tableName, now()->format('Y_m_d_His')
                ], $file);

            if ($this->files->exists($filePath)) {
                $this->info("File already exists, skipping: {$filePath}");
                continue;
            }

            $this->files->put($filePath, $content);
            $this->info("Created file: {$filePath}");
        }
    }

    /**
     * @param  string  $modulePath
     * @return void
     */
    protected function cleanupModule(string $modulePath): void
    {
        if ($this->files->exists($modulePath)) {
            $this->files->deleteDirectory($modulePath);
            $this->info("Cleaned up incomplete module at {$modulePath}.");
        }
    }

    /**
     * @param  string  $moduleName
     * @param  bool    $isApi
     * @return array
     */
    protected function getStubFiles(string $moduleName, bool $isApi): array
    {
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
            'Http/Resources/{{module}}Resource.php' =>
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

        return array_merge(
            $stubs,
            $this->getExceptionStubs($moduleName),
            $this->getRequestStubs($moduleName),
        );
    }

    /**
     * @param  string  $moduleName
     * @return array
     */
    protected function getExceptionStubs(string $moduleName): array
    {
        return [
            "Exceptions/{$moduleName}DestroyException.php" => base_path('stubs/module/Exceptions/DestroyException.stub'),
            "Exceptions/{$moduleName}IndexException.php" => base_path('stubs/module/Exceptions/IndexException.stub'),
            "Exceptions/{$moduleName}NotFoundException.php" => base_path('stubs/module/Exceptions/NotFoundException.stub'),
            "Exceptions/{$moduleName}SearchException.php" => base_path('stubs/module/Exceptions/SearchException.stub'),
            "Exceptions/{$moduleName}StoreException.php" => base_path('stubs/module/Exceptions/StoreException.stub'),
            "Exceptions/{$moduleName}UpdateException.php" => base_path('stubs/module/Exceptions/UpdateException.stub'),
        ];
    }

    /**
     * @param  string  $moduleName
     * @return array
     */
    protected function getRequestStubs(string $moduleName): array
    {
        return [
            "Http/Requests/Create{$moduleName}Request.php" => base_path('stubs/module/Request/CreateRequest.stub'),
            "Http/Requests/Delete{$moduleName}Request.php" => base_path('stubs/module/Request/DeleteRequest.stub'),
            "Http/Requests/Search{$moduleName}Request.php" => base_path('stubs/module/Request/SearchRequest.stub'),
            "Http/Requests/Show{$moduleName}Request.php" => base_path('stubs/module/Request/ShowRequest.stub'),
            "Http/Requests/Update{$moduleName}Request.php" => base_path('stubs/module/Request/UpdateRequest.stub'),
        ];
    }

    /**
     * @param  string  $moduleName
     * @return void
     * @throws FileNotFoundException
     */
    protected function updateRepositoryServiceProvider(string $moduleName): void
    {
        $providerPath = app_path('Providers/RepositoryServiceProvider.php');
        $interface = "App\\Modules\\{$moduleName}\\Interfaces\\{$moduleName}Interface";
        $repository = "App\\Modules\\{$moduleName}\\Repositories\\{$moduleName}Repository";

        if (!$this->files->exists($providerPath)) {
            $this->error("RepositoryServiceProvider.php not found at {$providerPath}");
            return;
        }

        $content = $this->files->get($providerPath);
        $pattern = '/protected\s+array\s+\$repositories\s*=\s*\[(.*?)\];/s';

        if (preg_match($pattern, $content, $matches)) {
            $existingEntries = trim($matches[1]);
            $newEntry = "        \\{$interface}::class => \\{$repository}::class,";

            if (str_contains($existingEntries, $newEntry)) {
                $this->info("Entry for {$interface} already exists in RepositoryServiceProvider.php");
                return;
            }

            $updatedEntries = $existingEntries ? "{$existingEntries}\n{$newEntry}" : $newEntry;
            $replacement = "protected array \$repositories = [\n{$updatedEntries}\n];";
            $content = preg_replace($pattern, $replacement, $content);

            $this->files->put($providerPath, $content);
            $this->info("Successfully updated RepositoryServiceProvider with {$interface} binding.");
        } else {
            $this->error("Could not locate \$repositories array in RepositoryServiceProvider.php");
        }
    }
}
