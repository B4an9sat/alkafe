<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class AdminUserSeeder extends Seeder
{
    public function run(): void
    {
     
        User::firstOrCreate(
            ['email' => 'admin@poskafe.com'], 
            [
                'name' => 'Admin Utama',
                'password' => Hash::make('password'),
                'role' => 'admin',
            ]
        );

       
        User::firstOrCreate(
            ['email' => 'manager@poskafe.com'],
            [
                'name' => 'Manager Kafe',
                'password' => Hash::make('password'),
                'role' => 'manager',
            ]
        );

       
        User::firstOrCreate(
            ['email' => 'kasir@poskafe.com'],
            [
                'name' => 'Kasir Contoh',
                'password' => Hash::make('password'),
                'role' => 'kasir',
            ]
        );
    }
}