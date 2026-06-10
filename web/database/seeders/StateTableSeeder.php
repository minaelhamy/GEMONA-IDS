<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Exception;


class StateTableSeeder extends Seeder
{

    public function run()
    {
        $sql = database_path('locations/states.sql');

        if (!file_exists($sql)) {
            return;
        }

        $states = file_get_contents($sql);

        try {
            DB::unprepared($states);
        } catch (Exception $e) {
            Log::info($e->getMessage());
        }

    }
}