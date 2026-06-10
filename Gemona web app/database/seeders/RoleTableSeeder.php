<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;

class RoleTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run(): void
    {
        $roles = [
            [
                'name'       => 'Admin',
                'guard_name' => 'sanctum',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name'       => 'Customer',
                'guard_name' => 'sanctum',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name'       => 'Manager',
                'guard_name' => 'sanctum',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name'       => 'POS Operator',
                'guard_name' => 'sanctum',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name'       => 'Stuff',
                'guard_name' => 'sanctum',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ];

        foreach ($roles as $role) {
            Role::create($role);
        }
    }
}