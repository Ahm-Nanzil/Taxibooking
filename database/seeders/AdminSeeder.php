<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminSeeder extends Seeder
{
    public function run(): void
    {
        User::create([
            'name' => 'Admin',
            'email' => 'admin@ridexpress.com',
            'password' => Hash::make('Admin@123'),
            'phone' => '1234567890',
            'is_admin' => true,
            'email_verified_at' => now(),
        ]);
    }
}
