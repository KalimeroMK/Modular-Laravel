<?php

declare(strict_types=1);

namespace Tests\Feature\Modules;

use App\Modules\Core\Support\Generators\ModuleConfigUpdater;
use App\Modules\Core\Support\Generators\ModuleGenerationTracker;
use App\Modules\Core\Support\Generators\ModuleGenerator;
use Illuminate\Filesystem\Filesystem;
use Tests\TestCase;

class ModuleGenerationSnapshotTest extends TestCase
{
    private Filesystem $files;

    private ModuleGenerationTracker $tracker;

    private string $testModuleName = 'SnapshotTestModule';

    protected function setUp(): void
    {
        parent::setUp();
        $this->files = new Filesystem;
        $this->tracker = new ModuleGenerationTracker($this->files, new ModuleConfigUpdater);
    }

    protected function tearDown(): void
    {

        $modulePath = app_path("Modules/{$this->testModuleName}");
        if ($this->files->exists($modulePath)) {
            $this->files->deleteDirectory($modulePath);
        }

        $testPath = base_path("tests/Feature/Modules/{$this->testModuleName}");
        if ($this->files->exists($testPath)) {
            $this->files->deleteDirectory($testPath);
        }

        $bootstrapPath = base_path('bootstrap/app.php');
        if ($this->files->exists($bootstrapPath)) {
            $content = $this->files->get($bootstrapPath);
            $content = preg_replace('/\s+App\\\\Modules\\\\SnapshotTestModule\\\\Infrastructure\\\\Providers\\\\SnapshotTestModuleModuleServiceProvider::class,?\s*/', '', $content);
            $this->files->put($bootstrapPath, $content);
        }

        parent::tearDown();
    }

    public function test_generated_model_matches_expected_structure(): void
    {
        $generator = app(ModuleGenerator::class);

        $fields = [
            ['name' => 'name', 'type' => 'string'],
            ['name' => 'price', 'type' => 'float'],
            ['name' => 'is_active', 'type' => 'boolean'],
        ];

        $options = [
            'table' => 'snapshot_test_modules',
            'tracker' => $this->tracker,
        ];

        $generator->generate($this->testModuleName, $fields, $options);

        $modelPath = app_path("Modules/{$this->testModuleName}/Infrastructure/Models/{$this->testModuleName}.php");
        $content = $this->files->get($modelPath);

        $this->assertStringContainsString("class {$this->testModuleName}", $content);
        $this->assertStringContainsString('extends Model', $content);
        $this->assertStringContainsString("protected \$table = 'snapshot_test_modules'", $content);

        $this->assertStringContainsString("'name'", $content);
        $this->assertStringContainsString("'price'", $content);
        $this->assertStringContainsString("'is_active'", $content);

        $this->assertStringContainsString("'price' => 'float'", $content);
        $this->assertStringContainsString("'is_active' => 'bool'", $content);
    }

    public function test_generated_repository_interface_matches_expected_structure(): void
    {
        $generator = app(ModuleGenerator::class);

        $fields = [
            ['name' => 'name', 'type' => 'string'],
        ];

        $options = [
            'table' => 'snapshot_test_modules',
            'tracker' => $this->tracker,
        ];

        $generator->generate($this->testModuleName, $fields, $options);

        $interfacePath = app_path("Modules/{$this->testModuleName}/Infrastructure/Repositories/{$this->testModuleName}RepositoryInterface.php");
        $content = $this->files->get($interfacePath);

        $this->assertStringContainsString("interface {$this->testModuleName}RepositoryInterface", $content);
        $this->assertStringContainsString('extends RepositoryInterface', $content);
        $this->assertStringContainsString("namespace App\\Modules\\{$this->testModuleName}\\Infrastructure\\Repositories", $content);
    }

    public function test_generated_action_matches_expected_structure(): void
    {
        $generator = app(ModuleGenerator::class);

        $fields = [
            ['name' => 'name', 'type' => 'string'],
        ];

        $options = [
            'table' => 'snapshot_test_modules',
            'tracker' => $this->tracker,
        ];

        $generator->generate($this->testModuleName, $fields, $options);

        $actionPath = app_path("Modules/{$this->testModuleName}/Application/Actions/GetById{$this->testModuleName}Action.php");
        $content = $this->files->get($actionPath);

        $this->assertStringContainsString("class GetById{$this->testModuleName}Action", $content);
        $this->assertStringContainsString('extends AbstractGetByIdAction', $content);
    }

    public function test_generated_dto_matches_expected_structure(): void
    {
        $generator = app(ModuleGenerator::class);

        $fields = [
            ['name' => 'name', 'type' => 'string'],
            ['name' => 'price', 'type' => 'float'],
        ];

        $options = [
            'table' => 'snapshot_test_modules',
            'tracker' => $this->tracker,
        ];

        $generator->generate($this->testModuleName, $fields, $options);

        $dtoPath = app_path("Modules/{$this->testModuleName}/Application/DTO/{$this->testModuleName}DTO.php");
        $content = $this->files->get($dtoPath);

        $this->assertStringContainsString("class {$this->testModuleName}DTO", $content);
        $this->assertStringContainsString('public ?string $name', $content);
        $this->assertStringContainsString('public ?float $price', $content);
        $this->assertStringContainsString('public static function fromArray', $content);
        $this->assertStringContainsString('public function toArray', $content);
    }

    public function test_generated_controller_matches_expected_structure(): void
    {
        $generator = app(ModuleGenerator::class);

        $fields = [
            ['name' => 'name', 'type' => 'string'],
        ];

        $options = [
            'table' => 'snapshot_test_modules',
            'tracker' => $this->tracker,
        ];

        $generator->generate($this->testModuleName, $fields, $options);

        $controllerPath = app_path("Modules/{$this->testModuleName}/Infrastructure/Http/Controllers/{$this->testModuleName}Controller.php");
        $content = $this->files->get($controllerPath);

        $this->assertStringContainsString("class {$this->testModuleName}Controller", $content);
        $this->assertStringContainsString('extends AbstractCrudController', $content);
        $this->assertStringContainsString('protected function getCreateDtoClass', $content);
        $this->assertStringContainsString('protected function getUpdateDtoClass', $content);
        $this->assertStringContainsString('protected function getResourceClass', $content);
        $this->assertStringContainsString('protected function getEntityLabel', $content);
        $this->assertStringContainsString('public function store', $content);
        $this->assertStringContainsString('public function update', $content);
    }
}
