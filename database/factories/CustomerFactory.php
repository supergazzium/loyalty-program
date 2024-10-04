<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Customer>
 */
class CustomerFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            //

            'name' => $this->faker->name,
            'email' => $this->faker->unique()->safeEmail,
            'points' => $this->faker->numberBetween(0, 1000), // Random points
            'created_at' => now(),
            'updated_at' => now(),
        ];
    }
}
