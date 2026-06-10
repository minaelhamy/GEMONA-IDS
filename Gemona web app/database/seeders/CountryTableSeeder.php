<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Exception;


class CountryTableSeeder extends Seeder
{

    public function run()
    {
        $sql = database_path('locations/countries.sql');

        if (!file_exists($sql)) {
            return;
        }

        $countries = file_get_contents($sql);

        try {
            DB::unprepared($countries);
        } catch (Exception $e) {
            Log::info($e->getMessage());
        }

    }
}