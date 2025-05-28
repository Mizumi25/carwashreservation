<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Payment;

class PaymentTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        
        Payment::factory()->count(2)->create();
        
        Payment::factory()->create([
            'amount' => 100.00,
            'payment_method' => 'stripe',
            'payment_status' => 'fully_paid',
        ]);
    }
}
