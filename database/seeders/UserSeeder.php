<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Super Admin
        User::create([
            'name' => 'Super Admin',
            'email' => 'superadmin@upelkes.com',
            'password' => Hash::make('password'),
            'role' => 'super_admin',
        ]);

        // Admin
        User::create([
            'name' => 'Admin',
            'email' => 'admin@upelkes.com',
            'password' => Hash::make('password'),
            'role' => 'admin',
        ]);
    }
}
