<?php

namespace Database\Seeders;

use App\Models\Barcode;
use Illuminate\Database\Seeder;
use Dipokhalder\EnvEditor\EnvEditor;

class BarcodeTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Barcode::insert([
            [
                'name'       => 'EAN-13',
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'name'       => 'UPC-A',
                'created_at' => now(),
                'updated_at' => now()
            ],
        ]);
    }
}
