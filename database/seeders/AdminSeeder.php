<?php

namespace Database\Seeders;

use App\Enums\UserRole;
use App\Enums\UserStatus;
use App\Models\User;
use Illuminate\Database\Seeder;

class AdminSeeder extends Seeder
{
    public function run(): void
    {
        User::updateOrCreate(
            ['email' => 'admin@repomart.test'],
            [
                'name' => 'Admin',
                'password' => 'password',
                'role' => UserRole::Admin,
                'phone' => '09123456789',
                'status' => UserStatus::Active,
                'email_verified_at' => now(),
            ]
        );
    }
}
