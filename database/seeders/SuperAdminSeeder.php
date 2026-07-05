<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class SuperAdminSeeder extends Seeder
{
    public function run(): void
    {
        $admin = User::firstOrCreate(
            [
                'email' => 'codeywebinfo@gmail.com'
            ],
            [
                'name' => 'Super Admin',
                'password' => Hash::make('123@admin')
            ]
        );

        $admin->assignRole('Super Admin');
    }
}