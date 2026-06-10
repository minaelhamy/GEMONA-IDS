<?php

namespace Database\Seeders;


use App\Enums\Ask;
use Carbon\Carbon;
use App\Enums\Activity;
use App\Enums\CurrencyPosition;
use Illuminate\Database\Seeder;
use Dipokhalder\EnvEditor\EnvEditor;
use Illuminate\Support\Facades\Artisan;
use Dipokhalder\Settings\Facades\Settings;

class SiteTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $envService = new EnvEditor();
        Settings::group('site')->set([
            'site_date_format'                           => 'd-m-Y',
            'site_time_format'                           => 'h:i A',
            'site_default_timezone'                      => 'Asia/Dhaka',
            'site_default_ai_agent'                      => $envService->getValue('DEMO') ? 1 : 0,
            'site_default_currency'                      => 1,
            'site_default_currency_symbol'               => '$',
            'site_currency_position'                     => CurrencyPosition::LEFT,
            'site_digit_after_decimal_point'             => '2',
            'site_email_verification'                    => Activity::ENABLE,
            'site_phone_verification'                    => Activity::DISABLE,
            'site_default_language'                      => 1,
            'site_android_app_link'                      => $envService->getValue('DEMO') ? 'http://android.com' : '',
            'site_ios_app_link'                          => $envService->getValue('DEMO') ? 'http://ios.com' : '',
            'site_copyright'                             => $envService->getValue('DEMO') ? '© ShopKing by iNiLabs ' . Carbon::now()->year . ', All Rights Reserved' : '',
            'site_language_switch'                       => Activity::ENABLE,
            'site_app_debug'                             => Activity::DISABLE,
            'site_auto_update'                           => Activity::DISABLE,
            'site_online_payment_gateway'                => $envService->getValue('DEMO') ? Activity::ENABLE : Activity::DISABLE,
            'site_default_sms_gateway'                   => 0,
            'site_cash_on_delivery'                      => Activity::ENABLE,
            'site_non_purchase_product_maximum_quantity' => '100',
            'site_is_return_product_price_add_to_credit' => Ask::YES,
        ]);

        $envService->addData([
            'APP_DEBUG'              => 'false',
            'TIMEZONE'               => 'Asia/Dhaka',
            'CURRENCY'               => 'USD',
            'CURRENCY_SYMBOL'        => '$',
            'CURRENCY_POSITION'      => '5',
            'CURRENCY_DECIMAL_POINT' => '2',
            'DATE_FORMAT'            => 'd-m-Y',
            'TIME_FORMAT'            => 'h:i A',
            'NON_PURCHASE_QUANTITY'  => '100'
        ]);
        Artisan::call('optimize:clear');
    }
}
