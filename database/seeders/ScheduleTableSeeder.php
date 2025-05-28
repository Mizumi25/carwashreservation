<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Schedule;

class ScheduleTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create 2 random schedules
        Schedule::factory(2)->create();

        Schedule::factory()->create([
            'date' => '2024-10-01',
            'time_slot' => '10:00:00',
            'end_time' => '11:00:00',
        ]);

        Schedule::factory()->create([
            'date' => '2024-10-01',
            'time_slot' => '11:00:00',
            'end_time' => '12:00:00',
        ]);

        Schedule::factory()->create([
            'date' => '2024-10-01',
            'time_slot' => '12:00:00',
            'end_time' => '13:00:00',
        ]);
    }
}
