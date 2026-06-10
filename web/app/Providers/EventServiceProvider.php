<?php

namespace App\Providers;

use App\Events\SendSmsCode;
use App\Events\SendOrderSms;
use App\Events\SendEmailCode;
use App\Events\SendOrderMail;
use App\Events\SendOrderPush;
use App\Events\SendOrderGotSms;
use App\Events\SendOrderGotMail;
use App\Events\SendOrderGotPush;
use App\Events\SendVerifyEmailCode;
use Illuminate\Support\Facades\Event;
use Illuminate\Auth\Events\Registered;
use App\Listeners\SendSmsCodeNotification;
use App\Listeners\SendOrderSmsNotification;
use App\Listeners\SendEmailCodeNotification;
use App\Listeners\SendOrderMailNotification;
use App\Listeners\SendOrderPushNotification;
use App\Listeners\SendOrderGotSmsNotification;
use App\Listeners\SendOrderGotMailNotification;
use App\Listeners\SendOrderGotPushNotification;
use App\Listeners\SendVerifyEmailCodeNotification;
use Illuminate\Auth\Listeners\SendEmailVerificationNotification;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;

class EventServiceProvider extends ServiceProvider
{
    /**
     * The event to listener mappings for the application.
     *
     * @var array<class-string, array<int, class-string>>
     */
    protected $listen = [
        Registered::class => [
            SendEmailVerificationNotification::class,
        ],
        SendSmsCode::class              => [
            SendSmsCodeNotification::class
        ],
        SendEmailCode::class              => [
            SendEmailCodeNotification::class
        ],
        SendOrderMail::class            => [
            SendOrderMailNotification::class
        ],
        SendOrderSms::class             => [
            SendOrderSmsNotification::class
        ],
        SendOrderPush::class            => [
            SendOrderPushNotification::class
        ],
        SendOrderGotMail::class         => [
            SendOrderGotMailNotification::class
        ],
        SendOrderGotSms::class         => [
            SendOrderGotSmsNotification::class
        ],
        SendOrderGotPush::class         => [
            SendOrderGotPushNotification::class
        ],
        SendVerifyEmailCode::class              => [
            SendVerifyEmailCodeNotification::class
        ],
    ];

    /**
     * Register any events for your application.
     */
    public function boot(): void
    {
        //
    }

    /**
     * Determine if events and listeners should be automatically discovered.
     */
    public function shouldDiscoverEvents(): bool
    {
        return false;
    }
}