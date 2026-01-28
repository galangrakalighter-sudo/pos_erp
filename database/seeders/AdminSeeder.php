<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        User::create([
            'name' => 'Admin GAFI',
            'email' => 'admin@gafi.com',
            'password' => Hash::make('password'),
            'role' => 'admin',
        ]);

        User::create([
            'name' => 'Client Test',
            'email' => 'client@gafi.com',
            'password' => Hash::make('password'),
            'role' => 'client',
        ]);
    }
} 