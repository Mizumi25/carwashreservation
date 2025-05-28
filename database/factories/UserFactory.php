<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\User>
 */
class UserFactory extends Factory
{
    /**
     * The current password being used by the factory.
     */
    protected static ?string $password;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $names = [
            'Juan dela Cruz',
            'Maria Clara',
            'Jose Rizal',
            'Andres Bonifacio',
            'Gabriela Silang',
            'Lapu-Lapu',
            'Emilio Aguinaldo',
            'Melchora Aquino',
            'Apolinario Mabini',
            'Diego Silang',
            'Carlos P. Garcia',
            'Marcela Agoncillo',
            'Trinidad Tecson',
            'Leona Florentino',
            'Gregorio del Pilar'
        ];
        
        return [
            'name' => fake()->name(),
            'email' => fake()->unique()->safeEmail(),
            'email_verified_at' => now(),
            'profile_picture' => 'profile_pictures/defaultCarWash.png',
            'password' => static::$password ??= Hash::make('password'),
            'remember_token' => Str::random(10),
            'role' => 'client',
            'phone_number' => fake()->numerify('09##########'),
            'is_active' => false,
        ];
    }

    /**
     * Indicate that the model's email address should be unverified.
     */
    public function unverified(): static
    {
        return $this->state(fn (array $attributes) => [
            'email_verified_at' => null,
        ]);
    }
}
