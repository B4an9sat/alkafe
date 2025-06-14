<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Category; // Import model Category

class CategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $categories = [
            ['name' => 'Coffee'],
            ['name' => 'Non-Coffee Drinks'],
            ['name' => 'Food'],
            ['name' => 'Desserts'],
            ['name' => 'Snacks'],
        ];

        foreach ($categories as $category) {
            Category::firstOrCreate($category);
        }
    }
}