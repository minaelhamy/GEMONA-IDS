<?php

namespace App\Console\Commands;

use App\Enums\Ask;
use App\Enums\Status;
use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Schema;
use Spatie\Permission\Models\Permission;
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

        $role->syncPermissions(Permission::all());
        $user->syncRoles([$role->name]);

        $user->tokens()->delete();
        if (Schema::hasTable('sessions') && Schema::hasColumn('sessions', 'user_id')) {
            DB::table('sessions')->where('user_id', $user->id)->delete();
        }

        $passwordMatches = Hash::check($password, $user->password);

        $this->info("Admin user is ready: {$email}");
        $this->line("User ID: {$user->id}");
        $this->line("Status: {$user->status}");
        $this->line('Roles: ' . $user->roles()->pluck('name')->implode(', '));
        $this->line('Admin permissions: ' . $role->permissions()->count());
        $this->line('Password check: ' . ($passwordMatches ? 'OK' : 'FAILED'));
        $this->warn('Log in once, then change this password from the admin profile.');

        return $passwordMatches ? self::SUCCESS : self::FAILURE;
    }
}
