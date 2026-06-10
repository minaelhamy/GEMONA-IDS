<?php

namespace App\Listeners;

use Exception;
use App\Mail\SendOtp;
use App\Events\SendEmailCode;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class SendEmailCodeNotification
{
    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct()
    {
    }

    /**
     * Handle the event.
     *
     * @param \App\Events\SendEmailCode $event
     * @return void
     */
    public function handle(SendEmailCode $event)
    {
        try {
            Mail::to($event->info['email'])->send(new SendOtp($event->info['pin']));
        } catch (Exception $e) {
            Log::info($e->getMessage());
        }
    }
}