<?php

namespace Database\Seeders;


use Dipokhalder\EnvEditor\EnvEditor;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Artisan;
use Dipokhalder\Settings\Facades\Settings;

class LicenseTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run(): void
    {
        $envService = new EnvEditor();
        Settings::group('license')->set([
            'license_key' => $envService->getValue('VITE_API_KEY')
        ]);
        if ($envService->getValue('DEMO')) {
            Settings::group('license')->set([
                'license_key' => 'i9u99tt4-f0w6-71w7-8394-y968t02516r11'
            ]);
            $envService->addData(['VITE_API_KEY' => 'i9u99tt4-f0w6-71w7-8394-y968t02516r11']);
            Artisan::call('optimize:clear');
        }
    }
}
