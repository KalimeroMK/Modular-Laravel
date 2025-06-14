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
        protected ModelGenerator $modelGenerator,
        protected RequestGenerator $requestGenerator,
        protected ExceptionGenerator $exceptionGenerator,
        protected ActionGenerator $actionGenerator,
        protected ObserverGenerator $observerGenerator,
        protected PolicyGenerator $policyGenerator,
        protected FeatureTestGenerator $testGenerator,
        protected RepositoryBinder $repositoryBinder,
        protected FieldParser $fieldParser,
    ) {}

    /**
     * @throws FileNotFoundException
     */
    public function generate(string $moduleName, array $options): void
    {
        // Step 1: Build directory structure
        $this->structureBuilder->create($moduleName);

        // Step 2: Parse fields from model option
        $fields = $this->fieldParser->parse($options['model'] ?? '');

        // Step 3: Generate stub-based files
        $this->stubFileGenerator->generate($moduleName, $fields, $options);

        // Step 4: Generate DTO class
        $this->dtoGenerator->generate($moduleName, $fields);

        // Step 5: Generate Model class
        $this->modelGenerator->generate($moduleName, $fields, $options);

        // Step 6: Generate Request classes
        $this->requestGenerator->generate($moduleName, $fields);

        // Step 7: Generate Exception classes (optional)
        if (! empty($options['exceptions'])) {
            $this->exceptionGenerator->generate($moduleName);
        }

        // Step 8: Generate Action classes
        $this->actionGenerator->generate($moduleName);

        // Step 9: Generate Observer (optional)
        if (! empty($options['observers'])) {
            $this->observerGenerator->generate($moduleName);
        }

        // Step 10: Generate Policy (optional)
        if (! empty($options['policies'])) {
            $this->policyGenerator->generate($moduleName);
        }

        // Step 11: Generate Feature Test
        $this->testGenerator->generate($moduleName, $fields);

        // Step 12: Bind interface/repository to RepositoryServiceProvider
        $this->repositoryBinder->bind($moduleName);
    }
}
