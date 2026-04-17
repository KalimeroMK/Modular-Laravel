<?php

declare(strict_types=1);

namespace App\Modules\Core\Support\Generators;

class ModuleConfigUpdater
{
    protected string $configPath;

    public function __construct()
    {
        $this->configPath = config_path('modules.php');
    }

    public function addModule(string $moduleName): void
    {
        if (! file_exists($this->configPath)) {
            return;
        }

        $content = file_get_contents($this->configPath);
        if ($content === false) {
            return;
        }

        if ($this->moduleExists($content, $moduleName)) {
            return;
        }

        $newModuleEntry = $this->buildModuleEntry($moduleName);
        $content = $this->insertModuleEntry($content, $newModuleEntry);

        file_put_contents($this->configPath, $content);
    }

    public function removeModule(string $moduleName): void
    {
        if (! file_exists($this->configPath)) {
            return;
        }

        $content = file_get_contents($this->configPath);
        if ($content === false) {
            return;
        }

        $pattern = "/\s+'{$moduleName}' => \[\s+.*?\s+\],\n/s";
        $result = preg_replace($pattern, '', $content);
        if ($result === null) {
            return;
        }

        file_put_contents($this->configPath, $result);
    }

    protected function moduleExists(string $content, string $moduleName): bool
    {
        return str_contains($content, "'{$moduleName}' =>");
    }

    protected function buildModuleEntry(string $moduleName): string
    {
        return "        '{$moduleName}' => [\n            'enabled' => true,\n        ],";
    }

    protected function insertModuleEntry(string $content, string $newEntry): string
    {
        $start = mb_strpos($content, "    'specific' => [");
        if ($start === false) {
            return $content;
        }

        $end = mb_strpos($content, "\n    ],", $start);
        if ($end === false) {
            return $content;
        }

        return mb_substr($content, 0, $end)."\n".$newEntry.mb_substr($content, $end);
    }
}
