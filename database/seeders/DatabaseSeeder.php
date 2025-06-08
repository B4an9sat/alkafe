<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create(); // Ini adalah contoh default, bisa dihapus atau di-comment

        $this->call([
            AdminUserSeeder::class,
            CategorySeeder::class,
            ProductSeeder::class,
        ]);
    }
}
