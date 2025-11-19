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
        $this->structureBuilder->create($moduleName);

        $this->stubFileGenerator->generate($moduleName, $fields, $options);
        $this->dtoGenerator->generate($moduleName, $fields);
        $this->requestGenerator->generate($moduleName, $fields);

        if (! empty($options['exceptions'])) {
            $this->exceptionGenerator->generate($moduleName);
        }

        $this->actionGenerator->generate($moduleName, ! empty($options['events']));

        if (! empty($options['observers'])) {
            $this->observerGenerator->generate($moduleName);
        }

        if (! empty($options['policies'])) {
            $this->policyGenerator->generate($moduleName);
        }

        if (! empty($options['events'])) {
            $this->eventGenerator->generate($moduleName);
            $this->listenerGenerator->generate($moduleName);
        }

        if (! empty($options['enum'])) {
            $this->enumGenerator->generate($moduleName);
        }

        if (! empty($options['notifications'])) {
            $this->notificationGenerator->generate($moduleName);
        }

        $this->testGenerator->generate($moduleName, $fields, $options);
        $this->repositoryBinder->bind($moduleName);
    }
}
