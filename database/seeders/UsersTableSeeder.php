<?php

namespace Database\Seeders;

//use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;

class UsersTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create an admin user
        User::factory()->create([
            'name' => 'CarWashManagement',
            'email' => 'carwash@gmail.com',
            'profile_picture' => 'profile_pictures/AdminCarWash.jpeg',
            'password' => bcrypt('carwash123'), 
            'role' => 'admin',
            'phone_number' => '09123456789',
        ]);

        // Create a client user
        User::factory()->create([
            'name' => 'Client Test',
            'email' => 'client@gmail.com',
            'password' => bcrypt('client123'), 
            'role' => 'client',
        ]);
    }
}


