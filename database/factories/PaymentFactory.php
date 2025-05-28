<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Payment>
 */
class PaymentFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'amount' => $this->faker->randomFloat(2, 50, 1000), 
            'payment_method' => $this->faker->randomElement(['stripe', 'paypal', 'halfcash']), 
            'payment_status' => $this->faker->randomElement(['not_paid', 'partialy_paid', 'fully_paid']),
        ];
    }
}

