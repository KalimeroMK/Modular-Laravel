<?php

declare(strict_types=1);

namespace App\Modules\Core\Support\Generators;

use Illuminate\Filesystem\Filesystem;

class ModuleGenerationTracker
{
    protected array $generatedFiles = [];

    protected array $modifiedFiles = [];

    public function __construct(
        protected Filesystem $files,
        protected ModuleConfigUpdater $configUpdater
    ) {}

    public function trackGeneratedFile(string $moduleName, string $filePath): void
    {
        if (! isset($this->generatedFiles[$moduleName])) {
            $this->generatedFiles[$moduleName] = [];
        }

        $this->generatedFiles[$moduleName][] = $filePath;
    }

    public function trackModifiedFile(string $filePath, string $originalContent): void
    {
        if (! isset($this->modifiedFiles[$filePath])) {
            $this->modifiedFiles[$filePath] = $originalContent;
        }
    }

    public function getGeneratedFiles(string $moduleName): array
    {
        return $this->generatedFiles[$moduleName] ?? [];
    }

    public function getTrackedModules(): array
    {
        return array_keys($this->generatedFiles);
    }

    public function getModifiedFiles(): array
    {
        return $this->modifiedFiles;
    }

    public function rollbackModule(string $moduleName): void
    {
        $files = $this->getGeneratedFiles($moduleName);

        foreach ($files as $filePath) {
            if ($this->files->exists($filePath)) {
                $this->files->delete($filePath);
            }
        }

        $moduleBasePath = app_path("Modules/{$moduleName}");
        if ($this->files->exists($moduleBasePath)) {
            $this->files->deleteDirectory($moduleBasePath);
        }

        $testPath = base_path("tests/Feature/Modules/{$moduleName}");
        if ($this->files->exists($testPath)) {
            $this->files->deleteDirectory($testPath);
        }

        $this->configUpdater->removeModule($moduleName);

        unset($this->generatedFiles[$moduleName]);
    }

    public function restoreModifiedFiles(): void
    {
        foreach ($this->modifiedFiles as $filePath => $originalContent) {
            if ($this->files->exists($filePath)) {
                $this->files->put($filePath, $originalContent);
            }
        }
        $this->modifiedFiles = [];
    }

    public function rollbackAll(): void
    {
        $modules = $this->getTrackedModules();

        foreach ($modules as $moduleName) {
            $this->rollbackModule($moduleName);
        }

        $this->generatedFiles = [];
        $this->modifiedFiles = [];
    }

    public function getStatistics(): array
    {
        $totalFiles = 0;
        $filesByModule = [];

        foreach ($this->generatedFiles as $moduleName => $files) {
            $fileCount = count($files);
            $totalFiles += $fileCount;
            $filesByModule[$moduleName] = $fileCount;
        }

        return [
            'modules' => count($this->generatedFiles),
            'files' => $totalFiles,
            'files_by_module' => $filesByModule,
        ];
    }

    public function clear(): void
    {
        $this->generatedFiles = [];
        $this->modifiedFiles = [];
    }

    protected function deleteDirectoryIfEmpty(string $directory): void
    {
        if (! $this->files->exists($directory)) {
            return;
        }

        $files = $this->files->files($directory);
        $directories = $this->files->directories($directory);

        if (empty($files) && empty($directories)) {
            $this->files->deleteDirectory($directory);
        }
    }
}
