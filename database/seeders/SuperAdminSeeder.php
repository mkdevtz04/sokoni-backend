<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class SuperAdminSeeder extends Seeder
{
    public function run(): void
    {
        User::firstOrCreate(
            ['phone' => '0700000000'],
            [
                'first_name'         => 'Super',
                'last_name'          => 'Admin',
                'email'              => 'admin@sokoni.co.tz',
                'password'           => bcrypt('Admin@1234'),
                'role'               => 'super_admin',
                'status'             => 'active',
                'profile_completed'  => true,
                'phone_verified_at'  => now(),
                'email_verified_at'  => now(),
            ]
        );
    }
}
