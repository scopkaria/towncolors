<?php

namespace Database\Seeders;

use App\Enums\UserRole;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminUserSeeder extends Seeder
{
    public function run(): void
    {
        // Wipe all users then recreate demo accounts
        User::query()->delete();

        $users = [
            [
                'name'               => 'Demo Admin',
                'email'              => 'admin@towncore.local',
                'password'           => Hash::make('password'),
                'role'               => UserRole::ADMIN,
                'email_verified_at'  => now(),
            ],
            [
                'name'               => 'Demo Client',
                'email'              => 'client@towncore.local',
                'password'           => Hash::make('password'),
                'role'               => UserRole::CLIENT,
                'email_verified_at'  => now(),
            ],
            [
                'name'               => 'Demo Freelancer',
                'email'              => 'freelancer@towncore.local',
                'password'           => Hash::make('password'),
                'role'               => UserRole::FREELANCER,
                'email_verified_at'  => now(),
            ],
        ];

        foreach ($users as $data) {
            User::create($data);
        }
    }
}
