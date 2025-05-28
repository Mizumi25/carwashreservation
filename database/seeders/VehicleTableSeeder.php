<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Vehicle;

class VehicleTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create 4 random vehicles
        Vehicle::factory(4)->create();

        // Custom vehicle entries
        Vehicle::factory()->create([
            'user_id' => 1, 
            'vehicle_type_id' => 1, 
            'model' => 'Civic',
            'make' => 'Honda',
            'year' => 2022,
            'license_plate' => 'HND-1234',
            'color' => 'Red',
        ]);

        Vehicle::factory()->create([
            'user_id' => 2, 
            'vehicle_type_id' => 2, 
            'model' => 'Model S',
            'make' => 'Tesla',
            'year' => 2021,
            'license_plate' => 'TES-5678',
            'color' => 'Black',
        ]);
    }
}
