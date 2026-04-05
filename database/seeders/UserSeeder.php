<?php

namespace Database\Seeders;

use App\Enums\RoleEnum;
use App\Enums\StatusEnum;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        $users = [
            [
                'name' => 'Admin User',
                'email' => 'admin@finance.test',
                'password' => 'passadmin', // Let the model cast handle hashing
                'role' => RoleEnum::Admin,
                'status' => StatusEnum::Active,
            ],
            [
                'name' => 'Analyst User',
                'email' => 'analyst@finance.test',
                'password' => 'passanalyst', // Let the model cast handle hashing
                'role' => RoleEnum::Analyst,
                'status' => StatusEnum::Active,
            ],
            [
                'name' => 'Viewer User',
                'email' => 'viewer@finance.test',
                'password' => 'passviewer', // Let the model cast handle hashing
                'role' => RoleEnum::Viewer,
                'status' => StatusEnum::Active,
            ],
            [
                'name' => 'Inactive Analyst',
                'email' => 'inactive@finance.test',
                'password' => 'passinactive', // Let the model cast handle hashing
                'role' => RoleEnum::Analyst,
                'status' => StatusEnum::Inactive,
            ],
        ];

        foreach ($users as $userData) {
            User::updateOrCreate(
                ['email' => $userData['email']],
                $userData
            );
        }

        $this->command->info('Users seeded: admin, analyst, viewer, inactive analyst');
    }
}