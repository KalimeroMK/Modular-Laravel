<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
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

        // Instead of failing if the module folder exists, just warn and continue
        if ($this->files->exists($modulePath)) {
            $this->warn("Module '{$moduleName}' already exists. We'll only create missing files.");
        } else {
            $this->files->makeDirectory($modulePath, 0755, true);
        }

        try {
            // Create the sub-folders (will skip any that already exist)
            $this->createModuleStructure($modulePath, $isApi);

            // Generate missing files
            $this->generateFiles($modulePath, $moduleName, $isApi);

            // Update the RepositoryServiceProvider (if the interface binding isn't there yet)
            $this->updateRepositoryServiceProvider($moduleName);

            $this->info("Module '{$moduleName}' is now up-to-date!");
        } catch (\Exception $e) {
            // If it's a brand-new module and something fails, remove partial directory
            // only if the directory truly didn't exist before.
            if (!$this->files->exists($modulePath)) {
                $this->cleanupModule($modulePath);
            }
            $this->error("Failed to update/create module: {$e->getMessage()}");
            return 1;
        }

        return 0;
    }

    protected function createModuleStructure(string $modulePath, bool $isApi): void
    {
        // Shared structure (directories that are relevant for both web and API).
        $structure = [
            'Config',
            'Http/Requests',
            'Helpers',
            'Interfaces',
            'Models',
            'Repositories',
            'routes',
            'Services',
            'Traits',
            'database/migrations',
            'database/factories',
        ];

        // If it's an API module, add additional subfolders relevant to API usage
        // If it's an API module, add additional subfolders relevant to API usage
        if ($isApi) {
            $structure = array_merge($structure, [
                'Http/Controllers/Api',
                'Http/Resources',
                'Exceptions',
            ]);
        } else {
            // Add only for Web
            $structure = array_merge($structure, [
                'Resources/lang',
                'Resources/views/layouts',
                'Resources/views',
            ]);
        }

        // Create each directory if it doesn't already exist
        foreach ($structure as $directory) {
            $path = "$modulePath/$directory";
            if (!$this->files->exists($path)) {
                $this->files->makeDirectory($path, 0755, true);
            }
        }
    }

    protected function generateFiles(string $modulePath, string $moduleName, bool $isApi): void
    {
        $tableName = Str::plural(Str::snake($moduleName));

        // 1. Start with all stubs (both web and API).
        // 2. Then remove the web stubs if it's an API-only run,
        //    or remove the API stubs if it's a web-only run.
        $allStubs = [
            // -- Shared stubs
            "Interfaces/{{module}}Interface.php" =>
                base_path('stubs/module/Interface.stub'),
            "Repositories/{{module}}Repository.php" =>
                base_path('stubs/module/Repository.stub'),
            "Models/{{module}}.php" =>
                base_path('stubs/module/Model.stub'),
            "database/migrations/{{timestamp}}_create_{{table}}_table.php" =>
                base_path('stubs/module/Migration.stub'),
            "database/factories/{{module}}Factory.php" =>
                base_path('stubs/module/Factory.stub'),
            "Services/{{module}}Service.php" =>
                base_path('stubs/module/Service.stub'),

            // -- Web stubs
            "Http/Controllers/{{module}}Controller.php" =>
                base_path('stubs/module/Http/Controllers/Controller.stub'),
            "routes/web.php" =>
                base_path('stubs/module/routes/web.stub'),
            "Resources/views/index.blade.php" =>
                base_path('stubs/module/Resources/index.blade.stub'),
            "Resources/views/create.blade.php" =>
                base_path('stubs/module/Resources/create.blade.stub'),
            "Resources/views/show.blade.php" =>
                base_path('stubs/module/Resources/show.blade.stub'),
            "Resources/views/layouts/master.blade.php" =>
                base_path('stubs/module/Resources/master.blade.stub'),
        ];

        // API stubs (only needed if --api is set)
        $apiStubs = [
            "Http/Controllers/Api/{{module}}Controller.php" =>
                base_path('stubs/module/Http/Controllers/ApiController.stub'),
            "routes/api.php" =>
                base_path('stubs/module/routes/api.stub'),
            "Http/Resources/{{module}}Resource.php" =>
                base_path('stubs/module/Http/Resource/Resource.stub'),
        ];

        // Exception stubs for API
        $exceptionStubs = [
            "Exceptions/{{module}}DestroyException.php" =>
                base_path('stubs/module/Http/Exceptions/DestroyException.stub'),
            "Exceptions/{{module}}IndexException.php" =>
                base_path('stubs/module/Http/Exceptions/IndexException.stub'),
            "Exceptions/{{module}}NotFoundException.php" =>
                base_path('stubs/module/Http/Exceptions/NotFoundException.stub'),
            "Exceptions/{{module}}SearchException.php" =>
                base_path('stubs/module/Http/Exceptions/SearchException.stub'),
            "Exceptions/{{module}}StoreException.php" =>
                base_path('stubs/module/Http/Exceptions/StoreException.stub'),
            "Exceptions/{{module}}UpdateException.php" =>
                base_path('stubs/module/Http/Exceptions/UpdateException.stub'),
        ];

        // Request stubs (common to both but we list them here;
        // they're not strictly 'web' or 'api' only,
        // though you could treat them as needed).
        $requestStubs = [
            "Http/Requests/Create{{module}}Request.php" =>
                base_path('stubs/module/Http/Request/CreateRequest.stub'),
            "Http/Requests/Delete{{module}}Request.php" =>
                base_path('stubs/module/Http/Request/DeleteRequest.stub'),
            "Http/Requests/Search{{module}}Request.php" =>
                base_path('stubs/module/Http/Request/SearchRequest.stub'),
            "Http/Requests/Show{{module}}Request.php" =>
                base_path('stubs/module/Http/Request/ShowRequest.stub'),
            "Http/Requests/Update{{module}}Request.php" =>
                base_path('stubs/module/Http/Request/UpdateRequest.stub'),
        ];

        // Merge everything into $allStubs
        // Then if it's API, add $apiStubs + $exceptionStubs.
        // If not API, skip them.
        // In any case, add $requestStubs (assuming both web and API might use requests).
        if ($isApi) {
            $allStubs = array_merge($allStubs, $apiStubs, $exceptionStubs);
            // Optionally, remove Web stubs if you never want them in an API-only run:
            unset(
                $allStubs["Http/Controllers/{{module}}Controller.php"],
                $allStubs["routes/web.php"],
                $allStubs["Resources/views/index.blade.php"],
                $allStubs["Resources/views/create.blade.php"],
                $allStubs["Resources/views/show.blade.php"],
                $allStubs["Resources/views/layouts/master.blade.php"]
            );
        } else {
            // If it's NOT API, remove the API stubs
            unset(
                $apiStubs["Http/Controllers/Api/{{module}}Controller.php"],
                $apiStubs["routes/api.php"],
                $apiStubs["Http/Resources/{{module}}Resource.php"]
            );
            unset(
                $exceptionStubs["Exceptions/{{module}}DestroyException.php"],
                $exceptionStubs["Exceptions/{{module}}IndexException.php"],
                $exceptionStubs["Exceptions/{{module}}NotFoundException.php"],
                $exceptionStubs["Exceptions/{{module}}SearchException.php"],
                $exceptionStubs["Exceptions/{{module}}StoreException.php"],
                $exceptionStubs["Exceptions/{{module}}UpdateException.php"]
            );
        }

        // Add Request stubs to the final array
        $allStubs = array_merge($allStubs, $requestStubs);

        // Now generate all stubs in the final array
        foreach ($allStubs as $file => $stubPath) {
            if (!$this->files->exists($stubPath)) {
                $this->error("Stub file not found: {$stubPath}");
                continue;
            }

            $stubContent = $this->files->get($stubPath);

            // Perform replacements
            $content = str_replace(
                ['{{module}}', '{{module_lower}}', '{{table}}', '{{timestamp}}'],
                [
                    $moduleName,
                    Str::lower($moduleName),
                    $tableName,
                    now()->format('Y_m_d_His')
                ],
                $stubContent
            );

            $filePath = "$modulePath/" . str_replace(
                    ['{{module}}', '{{table}}', '{{timestamp}}'],
                    [
                        $moduleName,
                        $tableName,
                        now()->format('Y_m_d_His')
                    ],
                    $file
                );

            // If file already exists, skip it
            if ($this->files->exists($filePath)) {
                $this->info("File already exists, skipping: {$filePath}");
                continue;
            }

            // Otherwise, create it
            $this->files->ensureDirectoryExists(dirname($filePath));
            $this->files->put($filePath, $content);
            $this->info("Created file: {$filePath}");
        }
    }

    protected function cleanupModule(string $modulePath): void
    {
        if ($this->files->exists($modulePath)) {
            $this->files->deleteDirectory($modulePath);
            $this->info("Cleaned up incomplete module at {$modulePath}.");
        }
    }

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

            // If it’s already there, skip
            if (Str::contains($existingEntries, $newEntry)) {
                $this->info("Entry for {$interface} already exists in RepositoryServiceProvider.php");
                return;
            }

            // Otherwise, add it
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
