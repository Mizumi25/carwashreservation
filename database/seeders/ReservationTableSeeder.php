<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Reservation;

class ReservationTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Reservation::factory()->create([
            'user_id' => 1, 
            'vehicle_id' => 1, 
            'service_id' => 1, 
            'package_id' => null, 
            'schedule_id' => 1, 
            'payment_id' => 1, 
            'reservation_date' => now()->toDateString(), 
            'status' => 'pending', 
        ]);
    
        Reservation::factory()->create([
            'user_id' => 1, 
            'vehicle_id' => 1, 
            'service_id' => null, 
            'package_id' => 1, 
            'schedule_id' => 1, 
            'payment_id' => 1, 
            'reservation_date' => now()->toDateString(), 
            'status' => 'pending', 
        ]);
    
        Reservation::factory(2)->create(); 
    }
}
