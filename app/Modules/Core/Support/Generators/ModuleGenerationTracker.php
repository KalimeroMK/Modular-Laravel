<?php

declare(strict_types=1);

namespace App\Modules\Core\Support\Generators;

use Illuminate\Filesystem\Filesystem;

/**
 * Tracks generated files for rollback functionality.
 */
class ModuleGenerationTracker
{
    /**
     * @var array<string, array<string>>
     */
    protected array $generatedFiles = [];

    /**
     * @var array<string, string>
     */
    protected array $modifiedFiles = [];

    public function __construct(protected Filesystem $files) {}

    /**
     * Track a generated file.
     */
    public function trackGeneratedFile(string $moduleName, string $filePath): void
    {
        if (! isset($this->generatedFiles[$moduleName])) {
            $this->generatedFiles[$moduleName] = [];
        }

        $this->generatedFiles[$moduleName][] = $filePath;
    }

    /**
     * Track a modified file (for rollback).
     */
    public function trackModifiedFile(string $filePath, string $originalContent): void
    {
        if (! isset($this->modifiedFiles[$filePath])) {
            $this->modifiedFiles[$filePath] = $originalContent;
        }
    }

    /**
     * Get all generated files for a module.
     *
     * @return array<string>
     */
    public function getGeneratedFiles(string $moduleName): array
    {
        return $this->generatedFiles[$moduleName] ?? [];
    }

    /**
     * Get all tracked modules.
     *
     * @return array<string>
     */
    public function getTrackedModules(): array
    {
        return array_keys($this->generatedFiles);
    }

    /**
     * Get all modified files.
     *
     * @return array<string, string>
     */
    public function getModifiedFiles(): array
    {
        return $this->modifiedFiles;
    }

    /**
     * Rollback all changes for a module.
     */
    public function rollbackModule(string $moduleName): void
    {
        $files = $this->getGeneratedFiles($moduleName);

        foreach ($files as $filePath) {
            if ($this->files->exists($filePath)) {
                $this->files->delete($filePath);
            }
        }

        // Remove module directory recursively
        $moduleBasePath = app_path("Modules/{$moduleName}");
        if ($this->files->exists($moduleBasePath)) {
            $this->files->deleteDirectory($moduleBasePath);
        }

        // Remove test directory
        $testPath = base_path("tests/Feature/Modules/{$moduleName}");
        if ($this->files->exists($testPath)) {
            $this->files->deleteDirectory($testPath);
        }

        unset($this->generatedFiles[$moduleName]);
    }

    /**
     * Restore modified files (call after all rollbacks).
     */
    public function restoreModifiedFiles(): void
    {
        foreach ($this->modifiedFiles as $filePath => $originalContent) {
            if ($this->files->exists($filePath)) {
                $this->files->put($filePath, $originalContent);
            }
        }
        $this->modifiedFiles = [];
    }

    /**
     * Rollback all tracked changes.
     */
    public function rollbackAll(): void
    {
        $modules = $this->getTrackedModules();

        foreach ($modules as $moduleName) {
            $this->rollbackModule($moduleName);
        }

        $this->generatedFiles = [];
        $this->modifiedFiles = [];
    }

    /**
     * Get statistics about generated files.
     *
     * @return array{modules: int, files: int, files_by_module: array<string, int>}
     */
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

    /**
     * Clear all tracking data.
     */
    public function clear(): void
    {
        $this->generatedFiles = [];
        $this->modifiedFiles = [];
    }

    /**
     * Delete directory if it's empty.
     */
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
