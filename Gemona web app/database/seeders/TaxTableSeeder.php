<?php

namespace Database\Seeders;

use App\Models\Tax;
use Illuminate\Database\Seeder;
use Dipokhalder\EnvEditor\EnvEditor;

class TaxTableSeeder extends Seeder
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
            Tax::insert([
                [
                    'name'       => 'No-VAT',
                    'code'       => 'VAT-0%',
                    'tax_rate'   => 0,
                    'created_at' => now(),
                    'updated_at' => now()
                ],
                [
                    'name'       => 'VAT-5',
                    'code'       => 'VAT-5%',
                    'tax_rate'   => 5,
                    'created_at' => now(),
                    'updated_at' => now()
                ],
                [
                    'name'       => 'VAT-10',
                    'code'       => 'VAT-10%',
                    'tax_rate'   => 10,
                    'created_at' => now(),
                    'updated_at' => now()
                ],
                [
                    'name'       => 'VAT-20',
                    'code'       => 'VAT-20%',
                    'tax_rate'   => 20,
                    'created_at' => now(),
                    'updated_at' => now()
                ],
            ]);
        }
    }
}
