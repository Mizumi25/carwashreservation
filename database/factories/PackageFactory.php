<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Package>
 */
class PackageFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => $this->faker->word(),
            'description' => $this->faker->sentence(),
            'discount' => $this->faker->numberBetween(0, 50),
            'original_price' => $this->faker->randomFloat(2, 100, 5000),
            'duration' => fake()->numberBetween(30, 180), 
        ];
    }
}
