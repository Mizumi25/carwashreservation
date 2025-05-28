<?php

namespace Database\Factories;

use App\Models\Reservation;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Reservation>
 */
class ReservationFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $useService = $this->faker->boolean; 
        
        return [
            'user_id' => \App\Models\User::factory(),
            'vehicle_id' => \App\Models\Vehicle::factory(),
            'service_id' => $useService ? \App\Models\Service::factory() : null, 
            'package_id' => $useService ? null : \App\Models\Package::factory(), 
            'schedule_id' => \App\Models\Schedule::factory(),
            'payment_id' => \App\Models\Payment::factory(),
            'reservation_date' => $this->faker->date(),
            'status' => $this->faker->randomElement(['decline', 'approve', 'pending', 'done', 'not_appeared', 'cancelled', 'ongoing', 'completed']),
        ];
    }
}


