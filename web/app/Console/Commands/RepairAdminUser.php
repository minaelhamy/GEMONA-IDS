<?php

namespace App\Console\Commands;

use App\Enums\Ask;
use App\Enums\Status;
use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;

class RepairAdminUser extends Command
{
    protected $signature = 'gemona:repair-admin
        {--email=admin@example.com : Admin email address}
        {--password=123456 : Admin password}
        {--name=GEMONA Admin : Admin display name}';

    protected $description = 'Create or reset the primary GEMONA admin user.';

    public function handle(): int
    {
        $email = (string) $this->option('email');
        $password = (string) $this->option('password');
        $name = (string) $this->option('name');

        $role = Role::firstOrCreate(
            ['name' => 'Admin', 'guard_name' => 'sanctum'],
            ['created_at' => now(), 'updated_at' => now()]
        );

        $user = User::updateOrCreate(
            ['email' => $email],
            [
                'name' => $name,
                'phone' => '1000000000',
                'username' => 'admin',
                'email_verified_at' => now(),
                'password' => Hash::make($password),
                'status' => Status::ACTIVE,
                'country_code' => '+20',
                'is_guest' => Ask::NO,
            ]
        );

        if (!$user->hasRole($role->name)) {
            $user->assignRole($role);
        }

        $this->info("Admin user is ready: {$email}");
        $this->warn('Log in once, then change this password from the admin profile.');

        return self::SUCCESS;
    }
}
