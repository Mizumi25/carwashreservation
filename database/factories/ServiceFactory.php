<?php

namespace Database\Factories;

use App\Models\Service;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Service>
 */
class ServiceFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Service::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'service_name' => fake()->word() . ' Service',
            'icon' => 'service_icons/defaultServiceIcon.png', 
            'description' => fake()->sentence(),
            'price' => fake()->randomFloat(2, 10, 100), 
            'duration' => fake()->numberBetween(30, 180), 
            'is_active' => true,
            'category_id' => \App\Models\Category::factory(),
            'popularity' => 0, 
        ];
    }

    /**
     * Indicate that the service is inactive.
     */
    public function inactive(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_active' => false,
        ]);
    }
}



