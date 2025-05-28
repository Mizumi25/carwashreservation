<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Service;

class ServiceTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        
        Service::factory()->create([
            'service_name' => 'Basic Car Wash',
            'description' => 'A basic exterior wash to keep your car looking clean.',
            'price' => 20.00,
            'duration' => 30, 
            'icon' => 'service_icons/service1.png',
        ]);

        Service::factory()->create([
            'service_name' => 'Luxury Car Wash',
            'description' => 'Complete cleaning and detailing of the vehicle\'s interior.',
            'price' => 75.00,
            'duration' => 90, 
            'icon' => 'service_icons/service2.png',
        ]);

        Service::factory()->create([
            'service_name' => 'Expensive Cleaning',
            'description' => 'Thorough cleaning of the engine for optimal performance.',
            'price' => 50.00,
            'duration' => 60, 
            'icon' => 'service_icons/service3.png',
        ]);

        Service::factory()->create([
            'service_name' => 'Wax and Polish',
            'description' => 'Professional waxing and polishing for a shiny finish.',
            'price' => 40.00,
            'duration' => 45, 
            'icon' => 'service_icons/service4.png',
        ]);

        Service::factory()->create([
            'service_name' => 'Tire Cleaning',
            'description' => 'Rotation of tires to ensure even wear.',
            'price' => 30.00,
            'duration' => 30, 
            'icon' => 'service_icons/service5.png',
        ]);
    }
}
