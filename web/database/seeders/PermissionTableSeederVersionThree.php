<?php

namespace Database\Seeders;

use App\Libraries\AppLibrary;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use App\Enums\Role as EnumRole;

class PermissionTableSeederVersionThree extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run()
    {
        $permissions = [
            [
                'title'      => 'AI Assistant',
                'name'       => 'ai-assistant',
                'guard_name' => 'sanctum',
                'url'        => 'ai-assistant',
                'created_at' => now(),
                'updated_at' => now(),
            ]
        ];

        $permissions = AppLibrary::associativeToNumericArrayBuilder($permissions);
        Permission::insert($permissions);

        $adminRole = Role::find(EnumRole::ADMIN);
        $adminRole?->givePermissionTo(Permission::where('name', 'ai-assistant')->first());
    }
}
