<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Arr;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Priority>
 */
class PriorityFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'full_name' => fake()->words(3, true),
            'category_of_position' => fake()->word(),
            'position' => fake()->word(),
            'branch' => fake()->word(),
            'permit' => fake()->word(), //equal to 'training program' name
            'passed_at' => now()->addDays(Arr::random(range(-50, 50)))->addYear(range(-2, 1)),
        ];
    }
}
