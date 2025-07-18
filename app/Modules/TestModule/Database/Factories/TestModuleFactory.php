<?php

namespace App\Modules\TestModule\Database\Factories;

use App\Modules\TestModule\Models\TestModule;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<TestModule>
 */
class TestModuleFactory extends Factory
{
    protected $model = TestModule::class;

    public function definition(): array
    {
        return [
                        'name' => $this->faker->sentence,
            'description' => $this->faker->sentence,
            'is_active' => $this->faker->boolean,
        ];
    }
}
