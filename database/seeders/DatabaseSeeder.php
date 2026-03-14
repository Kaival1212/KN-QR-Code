<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Create admin account — change password immediately after first login
        User::firstOrCreate(
            ['email' => 'kaival@knconsulting.uk'],
            [
                'name' => 'KN Consulting',
                'password' => Hash::make(env('ADMIN_SEED_PASSWORD', 'ChangeMe123!')),
                'role' => 'admin',
                'email_verified_at' => now(),
            ]
        );
    }
}
