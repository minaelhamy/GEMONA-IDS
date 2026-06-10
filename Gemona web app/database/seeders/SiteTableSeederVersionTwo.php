<?php

namespace Database\Seeders;

use Dipokhalder\EnvEditor\EnvEditor;
use Dipokhalder\Settings\Facades\Settings;
use Illuminate\Database\Seeder;

class SiteTableSeederVersionTwo extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $envService = new EnvEditor();
        Settings::group('site')->set([
            'site_default_ai_agent' => $envService->getValue('DEMO') ? 1 : 0
        ]);
    }
}
