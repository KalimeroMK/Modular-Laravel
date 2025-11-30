<?php

namespace App\Modules\NonExistentStubModule\Database\Factories;

use App\Modules\NonExistentStubModule\Infrastructure\Models\NonExistentStubModule;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<NonExistentStubModule>
 */
class NonExistentStubModuleFactory extends Factory
{
    protected $model = NonExistentStubModule::class;

    public function definition(): array
    {
        return [
                        'name' => $this->faker->sentence,
        ];
    }
}
