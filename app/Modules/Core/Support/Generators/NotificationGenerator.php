<?php

declare(strict_types=1);

namespace App\Modules\Core\Support\Generators;

use Illuminate\Contracts\Filesystem\FileNotFoundException;
use Illuminate\Filesystem\Filesystem;

class NotificationGenerator
{
    public function __construct(protected Filesystem $files) {}

    /**
     * @throws FileNotFoundException
     */
    public function generate(string $moduleName): void
    {
        $notifications = [
            'Created' => 'ModelCreatedNotification',
            'Updated' => 'ModelUpdatedNotification',
            'Deleted' => 'ModelDeletedNotification',
        ];

        foreach ($notifications as $eventType => $stubName) {
            $className = $moduleName.$eventType.'Notification';
            $path = app_path("Modules/{$moduleName}/Application/Notifications/{$className}.php");
            $stubPath = base_path("stubs/module/Notifications/{$stubName}.stub");

            if (! $this->files->exists($stubPath)) {
                continue;
            }

            $replacements = [
                '{{module}}' => $moduleName,
                '{{moduleVar}}' => mb_strtolower($moduleName),
            ];

            $content = $this->files->get($stubPath);
            $content = str_replace(array_keys($replacements), array_values($replacements), $content);

            $this->files->ensureDirectoryExists(dirname($path));
            $this->files->put($path, $content);
        }
    }
}
