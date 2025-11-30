<?php

declare(strict_types=1);

namespace Tests\Feature\Modules;

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
        $this->tracker = new ModuleGenerationTracker($this->files);
    }

    protected function tearDown(): void
    {
        // Cleanup test module
        $modulePath = app_path("Modules/{$this->testModuleName}");
        if ($this->files->exists($modulePath)) {
            $this->files->deleteDirectory($modulePath);
        }

        // Cleanup test files
        $testPath = base_path("tests/Feature/Modules/{$this->testModuleName}");
        if ($this->files->exists($testPath)) {
            $this->files->deleteDirectory($testPath);
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

        // Verify class structure
        $this->assertStringContainsString("class {$this->testModuleName}", $content);
        $this->assertStringContainsString('extends Model', $content);
        $this->assertStringContainsString("protected \$table = 'snapshot_test_modules'", $content);

        // Verify fillable fields
        $this->assertStringContainsString("'name'", $content);
        $this->assertStringContainsString("'price'", $content);
        $this->assertStringContainsString("'is_active'", $content);

        // Verify casts
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

        // Verify interface structure
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

        // Verify action structure
        $this->assertStringContainsString("class GetById{$this->testModuleName}Action", $content);
        $this->assertStringContainsString('public function execute', $content);
        $this->assertStringContainsString("{$this->testModuleName}ResponseDTO", $content);
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

        // Verify DTO structure
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

        // Verify controller structure
        $this->assertStringContainsString("class {$this->testModuleName}Controller", $content);
        $this->assertStringContainsString('extends Controller', $content);
        $this->assertStringContainsString('public function index', $content);
        $this->assertStringContainsString('public function store', $content);
        $this->assertStringContainsString('public function show', $content);
        $this->assertStringContainsString('public function update', $content);
        $this->assertStringContainsString('public function destroy', $content);
    }
}
