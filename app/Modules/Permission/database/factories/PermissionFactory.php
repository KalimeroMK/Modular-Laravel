<?php

declare(strict_types=1);

namespace App\Modules\Permission\Database\Factories;

use App\Modules\Permission\Models\Permission;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Modules\Permission\Models\Permission>
 */
class PermissionFactory extends Factory
{
    protected $model = Permission::class;

    public function definition(): array
    {
        return [
            'name' => $this->faker->name(),
            'guard_name' => 'api',
        ];
    }
}
