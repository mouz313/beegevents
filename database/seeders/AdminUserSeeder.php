<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminUserSeeder extends Seeder
{
    public function run(): void
    {
        User::create([
            'name' => 'Admin',
            'email' => 'admin@beegevents.com',
            'password' => Hash::make('password'),
            'role' => 'admin',
            'phone' => '03000000000',
        ]);
    }
}
