<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        \App\Models\User::create([
            'name' => 'Owner',
            'email' => 'owner@petcare.com',
            'password' => bcrypt('password'),
            'role' => 'owner',
        ]);

        \App\Models\User::create([
            'name' => 'Admin',
            'email' => 'admin@petcare.com',
            'password' => bcrypt('password'),
            'role' => 'admin',
        ]);
    }
}
