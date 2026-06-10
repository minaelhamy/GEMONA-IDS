<?php

namespace Database\Seeders;

use App\Models\Supplier;
use Dipokhalder\EnvEditor\EnvEditor;
use Illuminate\Database\Seeder;


class SupplierTableSeeder extends Seeder
{
    public array $fashionSuppliers = [
        [
            'company'      => 'Puma Company',
            'name'         => 'Mr. Raj & DK',
            'email'        => 'rajdk@example.com',
            'country_code' => '+880',
            'phone'        => '12548723456',
            'address'      => 'Dhaka Bangladesh',
            'country'      => 'Bangladesh',
            'state'        => 'Dhaka',
            'city'         => 'Dhaka',
            'zip_code'     => '1216'
        ],
        [
            'company'      => 'Camper Company',
            'name'         => 'Md. Smith Pio',
            'email'        => 'smith@example.com',
            'country_code' => '+880',
            'phone'        => '12548797653',
            'address'      => 'Dhaka Bangladesh',
            'country'      => 'Bangladesh',
            'state'        => 'Dhaka',
            'city'         => 'Dhaka',
            'zip_code'     => '1216'
        ],
    ];

    public function run(): void
    {
        $envService = new EnvEditor();
        if ($envService->getValue('DEMO') && $envService->getValue('DISPLAY_TYPE') == 'fashion') {
            foreach ($this->fashionSuppliers as $fashionSupplier) {
                Supplier::create([
                    'company'      => $fashionSupplier['company'],
                    'name'         => $fashionSupplier['name'],
                    'email'        => $fashionSupplier['email'],
                    'country_code' => $fashionSupplier['country_code'],
                    'phone'        => $fashionSupplier['phone'],
                    'address'      => $fashionSupplier['address'],
                    'country'      => $fashionSupplier['country'],
                    'state'        => $fashionSupplier['state'],
                    'city'         => $fashionSupplier['city'],
                    'zip_code'     => $fashionSupplier['zip_code']
                ]);
            }
        }
    }
}
