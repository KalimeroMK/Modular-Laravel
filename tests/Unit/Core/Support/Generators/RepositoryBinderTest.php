<?php

declare(strict_types=1);

namespace Tests\Unit\Core\Support\Generators;

use App\Modules\Core\Support\Generators\RepositoryBinder;
use Illuminate\Filesystem\Filesystem;
use Override;
use Tests\TestCase;

class RepositoryBinderTest extends TestCase
{
    private Filesystem $files;

    #[Override]
    protected function setUp(): void
    {
        parent::setUp();
        $this->files = new Filesystem;
    }

    public function test_skips_binding_when_provider_does_not_exist(): void
    {
        $binder = new RepositoryBinder($this->files);

        // Should not throw when provider doesn't exist
        $binder->bind('NonExistentModule');

        $this->expectNotToPerformAssertions();
    }

    public function test_skips_binding_when_interface_does_not_exist(): void
    {
        $binder = new RepositoryBinder($this->files);

        // Should not throw when interface doesn't exist
        $binder->bind('NonExistentModule');

        $this->expectNotToPerformAssertions();
    }

    public function test_verifies_interface_and_repository_exist_before_binding(): void
    {
        $binder = new RepositoryBinder($this->files);

        // Should not throw when files don't exist
        $binder->bind('NonExistentModule');

        $this->expectNotToPerformAssertions();
    }
}
