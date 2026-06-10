<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Exception;


class CityTableSeeder extends Seeder
{

    public function run()
    {
        $sql = database_path('locations/cities.sql');

        if (!file_exists($sql)) {
            return;
        }

        $cities = file_get_contents($sql);

        try {
            DB::unprepared($cities);
        } catch (Exception $e) {
            Log::info($e->getMessage());
        }

    }
}