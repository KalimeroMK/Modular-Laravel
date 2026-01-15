<?php

declare(strict_types=1);

namespace Tests\Feature\Modules;

use App\Modules\Core\Support\Generators\ModuleGenerationTracker;
use App\Modules\Core\Support\Generators\ModuleGenerator;
use Illuminate\Filesystem\Filesystem;
use Override;
use Tests\TestCase;

class ModuleGenerationIntegrationTest extends TestCase
{
    private Filesystem $files;

    private ModuleGenerationTracker $tracker;

    private string $testModuleName = 'IntegrationTestModule';

    #[Override]
    protected function setUp(): void
    {
        parent::setUp();
        $this->files = new Filesystem;
        $this->tracker = new ModuleGenerationTracker($this->files);
    }

    #[Override]
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

        // Cleanup bootstrap/app.php - remove IntegrationTestModule provider registration
        $bootstrapPath = base_path('bootstrap/app.php');
        if ($this->files->exists($bootstrapPath)) {
            $content = $this->files->get($bootstrapPath);
            $content = preg_replace('/\s+App\\\\Modules\\\\IntegrationTestModule\\\\Infrastructure\\\\Providers\\\\IntegrationTestModuleModuleServiceProvider::class,?\s*/', '', $content);
            $this->files->put($bootstrapPath, $content);
        }

        parent::tearDown();
    }

    public function test_generates_complete_module_structure(): void
    {
        $generator = app(ModuleGenerator::class);

        $fields = [
            ['name' => 'name', 'type' => 'string'],
            ['name' => 'price', 'type' => 'float'],
        ];

        $options = [
            'table' => 'integration_test_modules',
            'tracker' => $this->tracker,
        ];

        $generator->generate($this->testModuleName, $fields, $options);

        // Verify core files exist
        $this->assertFileExists(app_path("Modules/{$this->testModuleName}/Infrastructure/Models/{$this->testModuleName}.php"));
        $this->assertFileExists(app_path("Modules/{$this->testModuleName}/Infrastructure/Repositories/{$this->testModuleName}RepositoryInterface.php"));
        $this->assertFileExists(app_path("Modules/{$this->testModuleName}/Infrastructure/Repositories/{$this->testModuleName}Repository.php"));
        $this->assertFileExists(app_path("Modules/{$this->testModuleName}/Application/DTO/{$this->testModuleName}DTO.php"));
        $this->assertFileExists(app_path("Modules/{$this->testModuleName}/Infrastructure/Http/Controllers/{$this->testModuleName}Controller.php"));

        // Verify actions exist
        $this->assertFileExists(app_path("Modules/{$this->testModuleName}/Application/Actions/Create{$this->testModuleName}Action.php"));
        $this->assertFileExists(app_path("Modules/{$this->testModuleName}/Application/Actions/GetById{$this->testModuleName}Action.php"));

        // Verify requests exist
        $this->assertFileExists(app_path("Modules/{$this->testModuleName}/Infrastructure/Http/Requests/Create{$this->testModuleName}Request.php"));
        $this->assertFileExists(app_path("Modules/{$this->testModuleName}/Infrastructure/Http/Requests/Update{$this->testModuleName}Request.php"));
    }

    public function test_generates_module_with_all_options(): void
    {
        $generator = app(ModuleGenerator::class);

        $fields = [
            ['name' => 'name', 'type' => 'string'],
        ];

        $options = [
            'table' => 'full_test_modules',
            'exceptions' => true,
            'observers' => true,
            'policies' => true,
            'events' => true,
            'enum' => true,
            'notifications' => true,
            'tracker' => $this->tracker,
        ];

        $generator->generate($this->testModuleName, $fields, $options);

        // Verify optional files exist
        $this->assertFileExists(app_path("Modules/{$this->testModuleName}/Infrastructure/Observers/{$this->testModuleName}Observer.php"));
        $this->assertFileExists(app_path("Modules/{$this->testModuleName}/Infrastructure/Policies/{$this->testModuleName}Policy.php"));
        $this->assertFileExists(app_path("Modules/{$this->testModuleName}/Enums/{$this->testModuleName}Status.php"));
        $this->assertFileExists(app_path("Modules/{$this->testModuleName}/Application/Events/{$this->testModuleName}Created.php"));
        // Notifications are generated only if stub files exist
        $notificationPath = app_path("Modules/{$this->testModuleName}/Application/Notifications/{$this->testModuleName}CreatedNotification.php");
        if (file_exists(base_path('stubs/module/Notifications/ModelCreatedNotification.stub'))) {
            $this->assertFileExists($notificationPath);
        }
    }

    public function test_tracks_all_generated_files(): void
    {
        $generator = app(ModuleGenerator::class);

        $fields = [
            ['name' => 'name', 'type' => 'string'],
        ];

        $options = [
            'table' => 'tracking_test_modules',
            'tracker' => $this->tracker,
        ];

        $generator->generate($this->testModuleName, $fields, $options);

        $stats = $this->tracker->getStatistics();

        $this->assertGreaterThan(0, $stats['files']);
        $this->assertEquals(1, $stats['modules']);
        $this->assertArrayHasKey($this->testModuleName, $stats['files_by_module']);
    }

    public function test_rollback_removes_generated_files(): void
    {
        $generator = app(ModuleGenerator::class);

        $fields = [
            ['name' => 'name', 'type' => 'string'],
        ];

        $options = [
            'table' => 'rollback_test_modules',
            'tracker' => $this->tracker,
        ];

        $generator->generate($this->testModuleName, $fields, $options);

        $modelPath = app_path("Modules/{$this->testModuleName}/Infrastructure/Models/{$this->testModuleName}.php");
        $this->assertFileExists($modelPath);

        // Rollback
        $this->tracker->rollbackModule($this->testModuleName);

        // Verify files are removed
        $this->assertFileDoesNotExist($modelPath);
    }
}
