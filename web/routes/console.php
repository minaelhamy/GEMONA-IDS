<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

if (config('supermarkets.auto_refresh_enabled')) {
    Schedule::command('supermarket:refresh --prices-only')
        ->dailyAt(env('SUPERMARKET_SYNC_TIME', '03:00'))
        ->withoutOverlapping();
}
