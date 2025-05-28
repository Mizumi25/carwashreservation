<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Package;
use App\Models\Service;

class PackageTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $services = Service::all();

        // Package::factory(10)->create()->each(function ($package) use ($services) {
        //     $package->services()->attach(
        //         $services->random(rand(1, 5))->pluck('id')->toArray()
        //     );
        // });


        Package::factory()->create([
            'name' => 'Basic CarWash + Wax & Polish',
            'description' => 'A combination of cheap car  wash with additional polish.',
        ]);

        Package::factory()->create([
            'name' => 'Luxury Car Wash',
            'description' => 'Complete cleaning and detailing of the vehicle\'s interior.',
            
        ]);

        Package::factory()->create([
            'name' => 'Expensive Cleaning',
            'description' => 'Thorough cleaning of the engine for optimal performance.',
        ]);
    }
}

