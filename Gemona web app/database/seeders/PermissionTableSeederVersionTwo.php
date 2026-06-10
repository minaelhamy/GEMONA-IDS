<?php

namespace Database\Seeders;

use Spatie\Permission\Models\Role;
use App\Libraries\AppLibrary;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use App\Enums\Role as EnumRole;


class PermissionTableSeederVersionTwo extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $permissions = [
            [
                'title'      => 'Reviews',
                'name'       => 'reviews',
                'guard_name' => 'sanctum',
                'url'        => 'reviews',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ];

        $permissions = AppLibrary::associativeToNumericArrayBuilder($permissions);
        Permission::insert($permissions);

        $adminPermissions = [
            ['name' => 'reviews'],
        ];

        $adminRole = Role::find(EnumRole::ADMIN);
        $adminPermissions = Permission::whereIn('name', $adminPermissions)->get();
        $adminRole?->givePermissionTo($adminPermissions);
    }
}
