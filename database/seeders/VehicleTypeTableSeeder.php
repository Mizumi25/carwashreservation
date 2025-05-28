<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\VehicleType;

class VehicleTypeTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        VehicleType::factory()->create([
            'name' => 'Motorcycle',
            'description' => 'A two wheeled vehicle design good for mobility.',
            'price' => 300.00,
            'icon' => 'vehicle_type_icons/vehicle1.png',
        ]);

        VehicleType::factory()->create([
            'name' => 'SUV',
            'description' => 'A larger vehicle suitable for off-road conditions.',
            'price' => 500.00,
            'icon' => 'vehicle_type_icons/vehicle2.png',
        ]);

        VehicleType::factory()->create([
            'name' => 'Truck',
            'description' => 'A vehicle designed primarily for transporting cargo.',
            'price' => 400.00,
            'icon' => 'vehicle_type_icons/vehicle3.png',
        ]);

        VehicleType::factory()->create([
            'name' => 'Tricycle',
            'description' => 'A three wheeled extension of a motorcycle meant for bigger passenger quantity.',
            'price' => 600.00,
            'icon' => 'vehicle_type_icons/vehicle4.png',
        ]);
    }
}
