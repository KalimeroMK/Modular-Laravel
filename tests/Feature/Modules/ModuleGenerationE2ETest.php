<?php

declare(strict_types=1);

namespace Tests\Feature\Modules;

use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\Facades\Artisan;
use Override;
use Tests\TestCase;

class ModuleGenerationE2ETest extends TestCase
{
    private Filesystem $files;

    private string $testModuleName = 'E2ETestModule';

    #[Override]
    protected function setUp(): void
    {
        parent::setUp();
        $this->files = new Filesystem;
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

        // Cleanup RepositoryServiceProvider changes
        $providerPath = app_path('Providers/RepositoryServiceProvider.php');
        if ($this->files->exists($providerPath)) {
            $content = $this->files->get($providerPath);
            $content = preg_replace('/use App\\\\Modules\\\\E2ETestModule.*?;/', '', $content);
            $content = preg_replace('/E2ETestModuleRepositoryInterface::class.*?E2ETestModuleRepository::class,?\s*/', '', (string) $content);
            $this->files->put($providerPath, $content);
        }

        // Cleanup bootstrap/app.php - remove E2ETestModule provider registration
        $bootstrapPath = base_path('bootstrap/app.php');
        if ($this->files->exists($bootstrapPath)) {
            $content = $this->files->get($bootstrapPath);
            $content = preg_replace('/\s+App\\\\Modules\\\\E2ETestModule\\\\Infrastructure\\\\Providers\\\\E2ETestModuleModuleServiceProvider::class,?\s*/', '', $content);
            $this->files->put($bootstrapPath, $content);
        }

        parent::tearDown();
    }

    public function test_make_module_command_generates_complete_module(): void
    {
        Artisan::call('make:module', [
            'name' => $this->testModuleName,
            '--model' => 'name:string,price:float',
            '--no-interaction' => true,
        ]);

        $exitCode = Artisan::call('make:module', [
            'name' => $this->testModuleName,
            '--model' => 'name:string,price:float',
            '--no-interaction' => true,
        ]);

        $this->assertEquals(0, $exitCode);

        // Verify core files
        $this->assertFileExists(app_path("Modules/{$this->testModuleName}/Infrastructure/Models/{$this->testModuleName}.php"));
        $this->assertFileExists(app_path("Modules/{$this->testModuleName}/Infrastructure/Repositories/{$this->testModuleName}RepositoryInterface.php"));
        $this->assertFileExists(app_path("Modules/{$this->testModuleName}/Application/Actions/Create{$this->testModuleName}Action.php"));
    }

    public function test_make_module_command_with_all_options(): void
    {
        $exitCode = Artisan::call('make:module', [
            'name' => $this->testModuleName,
            '--model' => 'name:string',
            '--exceptions' => true,
            '--observers' => true,
            '--policies' => true,
            '--events' => true,
            '--enum' => true,
            '--no-interaction' => true,
        ]);

        $this->assertEquals(0, $exitCode);

        // Verify optional files
        $this->assertFileExists(app_path("Modules/{$this->testModuleName}/Infrastructure/Observers/{$this->testModuleName}Observer.php"));
        $this->assertFileExists(app_path("Modules/{$this->testModuleName}/Infrastructure/Policies/{$this->testModuleName}Policy.php"));
        $this->assertFileExists(app_path("Modules/{$this->testModuleName}/Enums/{$this->testModuleName}Status.php"));
    }

    public function test_modules_build_from_yaml_command(): void
    {
        // Create temporary YAML file
        $yamlContent = <<<'YAML'
modules:
  E2ETestModule:
    fields:
      name: string
      price: float
    relations: []
    observers: true
    policies: true
YAML;

        $yamlPath = base_path('test_modules_e2e.yaml');
        file_put_contents($yamlPath, $yamlContent);

        try {
            Artisan::call('modules:build-from-yaml', [
                'file' => 'test_modules_e2e.yaml',
            ]);

            $exitCode = Artisan::call('modules:build-from-yaml', [
                'file' => 'test_modules_e2e.yaml',
            ]);

            $this->assertEquals(0, $exitCode);

            // Verify module was generated
            $this->assertFileExists(app_path("Modules/{$this->testModuleName}/Infrastructure/Models/{$this->testModuleName}.php"));
        } finally {
            // Cleanup YAML file
            if (file_exists($yamlPath)) {
                unlink($yamlPath);
            }
        }
    }
}
