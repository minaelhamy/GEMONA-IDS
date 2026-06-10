<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\MenuSection;

class MenuSectionTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        MenuSection::insert([
            [
                'name'          => 'Support Section',
                'created_at'       => now(),
                'updated_at'       => now()
            ],
            [
                'name'          => 'Legal Section',
                'created_at'       => now(),
                'updated_at'       => now()
            ],
        ]);
    }
}
