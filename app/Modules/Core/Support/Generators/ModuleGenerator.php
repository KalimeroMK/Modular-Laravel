<?php

declare(strict_types=1);

namespace App\Modules\Core\Support\Generators;

use Illuminate\Contracts\Filesystem\FileNotFoundException;

class ModuleGenerator
{
    public function __construct(
        protected ModuleStructureBuilder $structureBuilder,
        protected StubFileGenerator $stubFileGenerator,
        protected DTOGenerator $dtoGenerator,
        protected RequestGenerator $requestGenerator,
        protected ExceptionGenerator $exceptionGenerator,
        protected ActionGenerator $actionGenerator,
        protected ObserverGenerator $observerGenerator,
        protected PolicyGenerator $policyGenerator,
        protected EventGenerator $eventGenerator,
        protected ListenerGenerator $listenerGenerator,
        protected NotificationGenerator $notificationGenerator,
        protected FeatureTestGenerator $testGenerator,
        protected RepositoryBinder $repositoryBinder,
        protected FieldParser $fieldParser,
        protected EnumGenerator $enumGenerator,
    ) {}

    /**
     * @param  array<int, array{name: string, type: string, references?: string, on?: string}>  $fields
     * @param  array<string, mixed>  $options
     *
     * @throws FileNotFoundException
     */
    public function generate(string $moduleName, array $fields, array $options): void
    {
        $tracker = $options['tracker'] ?? null;

        $this->structureBuilder->create($moduleName);

        $this->stubFileGenerator->generate($moduleName, $fields, $options);
        $this->dtoGenerator->generate($moduleName, $fields);
        $this->trackFile($tracker, $moduleName, "Application/DTO/{$moduleName}DTO.php");
        $this->requestGenerator->generate($moduleName, $fields);
        $this->trackFile($tracker, $moduleName, "Infrastructure/Http/Requests/Create{$moduleName}Request.php");
        $this->trackFile($tracker, $moduleName, "Infrastructure/Http/Requests/Update{$moduleName}Request.php");

        if (! empty($options['exceptions'])) {
            $this->exceptionGenerator->generate($moduleName);
            $this->trackExceptionFiles($tracker, $moduleName);
        }

        $this->actionGenerator->generate($moduleName, ! empty($options['events']));
        $this->trackActionFiles($tracker, $moduleName);

        if (! empty($options['observers'])) {
            $this->observerGenerator->generate($moduleName);
            $this->trackFile($tracker, $moduleName, "Infrastructure/Observers/{$moduleName}Observer.php");
        }

        if (! empty($options['policies'])) {
            $this->policyGenerator->generate($moduleName);
            $this->trackFile($tracker, $moduleName, "Infrastructure/Policies/{$moduleName}Policy.php");
        }

        if (! empty($options['events'])) {
            $this->eventGenerator->generate($moduleName);
            $this->trackEventFiles($tracker, $moduleName);
            $this->listenerGenerator->generate($moduleName);
            $this->trackListenerFiles($tracker, $moduleName);
        }

        if (! empty($options['enum'])) {
            $this->enumGenerator->generate($moduleName);
            $this->trackFile($tracker, $moduleName, "Enums/{$moduleName}Status.php");
        }

        if (! empty($options['notifications'])) {
            $this->notificationGenerator->generate($moduleName);
            $this->trackNotificationFiles($tracker, $moduleName);
        }

        $this->testGenerator->generate($moduleName, $fields, $options);
        $this->trackFile($tracker, $moduleName, "../../../tests/Feature/Modules/{$moduleName}/{$moduleName}CrudTest.php");

        $this->repositoryBinder->bind($moduleName);
    }

    /**
     * Track generated file for rollback.
     */
    protected function trackFile(?ModuleGenerationTracker $tracker, string $moduleName, string $relativePath): void
    {
        if ($tracker instanceof ModuleGenerationTracker) {
            if (str_starts_with($relativePath, '../../../')) {
                $fullPath = base_path(str_replace('../../../', '', $relativePath));
            } else {
                $fullPath = app_path("Modules/{$moduleName}/{$relativePath}");
            }

            if (file_exists($fullPath)) {
                $tracker->trackGeneratedFile($moduleName, $fullPath);
            }
        }
    }

    /**
     * Track exception files.
     */
    protected function trackExceptionFiles(?ModuleGenerationTracker $tracker, string $moduleName): void
    {
        $exceptions = ['CreateException', 'UpdateException', 'DeleteException', 'IndexException', 'StoreException', 'DestroyException', 'NotFoundException'];
        foreach ($exceptions as $exception) {
            $this->trackFile($tracker, $moduleName, "Application/Exceptions/{$exception}.php");
        }
    }

    /**
     * Track action files.
     */
    protected function trackActionFiles(?ModuleGenerationTracker $tracker, string $moduleName): void
    {
        $actions = ['Create', 'Update', 'Delete', 'GetAll', 'GetById'];
        foreach ($actions as $action) {
            $this->trackFile($tracker, $moduleName, "Application/Actions/{$action}{$moduleName}Action.php");
        }
    }

    /**
     * Track event files.
     */
    protected function trackEventFiles(?ModuleGenerationTracker $tracker, string $moduleName): void
    {
        $events = ['Created', 'Updated', 'Deleted'];
        foreach ($events as $event) {
            $this->trackFile($tracker, $moduleName, "Application/Events/{$moduleName}{$event}.php");
        }
    }

    /**
     * Track listener files.
     */
    protected function trackListenerFiles(?ModuleGenerationTracker $tracker, string $moduleName): void
    {
        $events = ['Created', 'Updated', 'Deleted'];
        foreach ($events as $event) {
            $this->trackFile($tracker, $moduleName, "Application/Listeners/{$moduleName}{$event}Listener.php");
        }
    }

    /**
     * Track notification files.
     */
    protected function trackNotificationFiles(?ModuleGenerationTracker $tracker, string $moduleName): void
    {
        $notifications = ['Created', 'Updated', 'Deleted'];
        foreach ($notifications as $eventType) {
            $this->trackFile($tracker, $moduleName, "Application/Notifications/{$moduleName}{$eventType}Notification.php");
        }
    }
}
