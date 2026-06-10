<?php

namespace Database\Seeders;

use App\Enums\Status;
use App\Models\Outlet;
use Illuminate\Database\Seeder;
use Dipokhalder\EnvEditor\EnvEditor;

class OutletTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $envService = new EnvEditor();
        if ($envService->getValue('DEMO')) {
            Outlet::insert([
                [
                    'name'         => 'Mirpur 1',
                    'email'        => 'mirpuroutlet@example.com',
                    'country_code' => '+880',
                    'phone'        => '1325736364',
                    'latitude'     => '23.7956',
                    'longitude'    => '90.3537',
                    'city'         => "Dhaka",
                    'state'        => "Dhaka",
                    'zip_code'     => "1216",
                    'address'      => 'House :31, Road: 9, Block: A, Mirpur 1',
                    'status'       => Status::ACTIVE,
                    'created_at'   => now(),
                    'updated_at'   => now()
                ],
                [
                    'name'         => 'Dhanmondi 27',
                    'email'        => 'dhanmondioutlet@example.com',
                    'country_code' => '+880',
                    'phone'        => '1275362435',
                    'latitude'     => '23.7562',
                    'longitude'    => '90.3752',
                    'city'         => "Dhaka",
                    'state'        => "Dhaka",
                    'zip_code'     => "1209",
                    'address'      => "House :20, Road: 19, Block: B, Dhanmondi 32",
                    'status'       => Status::ACTIVE,
                    'created_at'   => now(),
                    'updated_at'   => now()
                ],
            ]);
        }
    }
}
