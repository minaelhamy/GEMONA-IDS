<?php

namespace App\Listeners;

use Exception;
use App\Mail\SendOtpMail;
use App\Events\SendVerifyEmailCode;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class SendVerifyEmailCodeNotification
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
     * @param \App\Events\SendVerifyEmailCode $event
     * @return void
     */
    public function handle(SendVerifyEmailCode $event)
    {
        try {
            Mail::to($event->info['email'])->send(new SendOtpMail($event->info['pin']));
        } catch (Exception $e) {
            Log::info($e->getMessage());
        }
    }
}