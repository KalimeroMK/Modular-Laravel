<?php

declare(strict_types=1);

namespace Tests;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;

class CleanSpatiePivotTablesTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Ensure Spatie model_has_permissions and model_has_roles tables are empty before running other tests.
     */
    public function test_clean_spatie_pivot_tables(): void
    {
        DB::table('model_has_permissions')->truncate();
        DB::table('model_has_roles')->truncate();
        $this->assertDatabaseCount('model_has_permissions', 0);
        $this->assertDatabaseCount('model_has_roles', 0);
    }
}
