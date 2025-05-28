<?php

namespace Database\Seeders;

use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Make Seeders and Call

        $this->call([
            UsersTableSeeder::class, 
            VehicleTypeTableSeeder::class,
            CategoryTableSeeder::class,
            ServiceTableSeeder::class,
            PackageTableSeeder::class,
            VehicleTableSeeder::class,
            ScheduleTableSeeder::class,
            PaymentTableSeeder::class,
            ReservationTableSeeder::class,
        ]);
    }
}



