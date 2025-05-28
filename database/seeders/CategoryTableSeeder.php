<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Category;

class CategoryTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $categories = [
            ['name' => 'Interior Cleaning', 'description' => 'Services for interior cleaning.'],
            ['name' => 'Exterior Cleaning', 'description' => 'Services for exterior cleaning.'],
        ];

        foreach ($categories as $category) {
            Category::create($category);
        }
    }
}
