<?php

declare(strict_types=1);

namespace App\Modules\NonExistentStubModule\Database\Factories;

use App\Modules\NonExistentStubModule\Infrastructure\Models\NonExistentStubModule;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<NonExistentStubModule>
 */
class NonExistentStubModuleFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var class-string<NonExistentStubModule>
     */
    protected $model = NonExistentStubModule::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
                        'name' => $this->faker->sentence,
        ];
    }
}
