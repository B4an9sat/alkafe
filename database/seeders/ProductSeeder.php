<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Product; // Import model Product
use App\Models\Category; // Import model Category

class ProductSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Pastikan kategori sudah ada, ambil ID-nya
        $coffeeCategory = Category::where('name', 'Coffee')->first();
        $foodCategory = Category::where('name', 'Food')->first();
        $dessertCategory = Category::where('name', 'Desserts')->first();

        // Buat produk-produk
        if ($coffeeCategory) {
            Product::firstOrCreate(
                ['name' => 'Espresso'],
                [
                    'sku' => 'COF001',
                    'description' => 'Single shot of pure coffee.',
                    'price' => 25000,
                    'stock' => 100,
                    'category_id' => $coffeeCategory->id,
                ]
            );
            Product::firstOrCreate(
                ['name' => 'Cappuccino'],
                [
                    'sku' => 'COF002',
                    'description' => 'Espresso with steamed milk foam.',
                    'price' => 35000,
                    'stock' => 80,
                    'category_id' => $coffeeCategory->id,
                ]
            );
        }

        if ($foodCategory) {
            Product::firstOrCreate(
                ['name' => 'Nasi Goreng'],
                [
                    'sku' => 'FOD001',
                    'description' => 'Indonesian fried rice with chicken.',
                    'price' => 40000,
                    'stock' => 50,
                    'category_id' => $foodCategory->id,
                ]
            );
        }

        if ($dessertCategory) {
            Product::firstOrCreate(
                ['name' => 'Chocolate Cake'],
                [
                    'sku' => 'DES001',
                    'description' => 'Rich chocolate cake slice.',
                    'price' => 30000,
                    'stock' => 30,
                    'category_id' => $dessertCategory->id,
                ]
            );
        }
    }
}