<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;

class RoleSeeder extends Seeder
{
    public function run(): void
    {
        Role::firstOrCreate([
            'name' => 'Super Admin',
            'guard_name' => 'web'
        ]);

        Role::firstOrCreate([
            'name' => 'Agent',
            'guard_name' => 'web'
        ]);
    }
}