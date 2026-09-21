<?php

namespace Database\Seeders;

use App\Models\Role;
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
        // 1. Ensure Admin Role exists
        $adminRole = Role::updateOrCreate(
            ['short_name' => 'admin'],
            [
                'name' => 'Admin',
                'description' => 'System Administrator with full access to all ERP modules',
                'status' => 1,
            ]
        );

        // 2. Ensure Admin User exists
        User::updateOrCreate(
            ['username' => 'admin'],
            [
                'role_id' => $adminRole->id,
                'first_name' => 'Super',
                'last_name' => 'Admin',
                'email' => 'admin@gmail.com',
                'password' => Hash::make('Admin123'),
                'phone_number' => '+91-9876543210',
                'address' => 'Plant Head Office',
                'status' => 1,
            ]
        );
    }
}
