<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Dipokhalder\Settings\Facades\Settings;

class ThemeTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Settings::group('theme')->set([
            'theme_logo'         => "",
            'theme_favicon_logo' => "",
            'theme_footer_logo'  => "",
        ]);
    }
}
