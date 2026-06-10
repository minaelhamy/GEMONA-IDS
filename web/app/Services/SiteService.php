<?php

namespace App\Services;


use App\Enums\Activity;
use App\Http\Requests\SiteRequest;
use App\Libraries\QueryExceptionLibrary;
use App\Models\Currency;
use Dipokhalder\EnvEditor\EnvEditor;
use Exception;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Log;
use Dipokhalder\Settings\Facades\Settings;

class SiteService
{
    public EnvEditor $envService;

    public function __construct(EnvEditor $envEditor)
    {
        $this->envService = $envEditor;
    }

    /**
     * @throws Exception
     */
    public function list()
    {
        try {
            return Settings::group('site')->all();
        } catch (Exception $exception) {
            Log::info($exception->getMessage());
            throw new Exception(QueryExceptionLibrary::message($exception), 422);
        }
    }

    /**
     * @throws Exception
     */
    public function update(SiteRequest $request)
    {
        try {
            $currency = Currency::find($request->site_default_currency);
            $app_debug = $this->envService->getValue('DEMO') ? Activity::DISABLE : $request->site_app_debug;

            $data = $request->validated();
            $data['site_default_currency_symbol'] = $currency->symbol;
            $data['site_app_debug'] = $app_debug;
            Settings::group('site')->set($data);

            $this->envService->addData([
                'APP_DEBUG'              => $app_debug == Activity::ENABLE ? 'true' : 'false',
                'TIMEZONE'               => $request->site_default_timezone,
                'CURRENCY'               => $currency?->code,
                'CURRENCY_SYMBOL'        => $currency?->symbol,
                'CURRENCY_POSITION'      => $request->site_currency_position,
                'CURRENCY_DECIMAL_POINT' => $request->site_digit_after_decimal_point,
                'DATE_FORMAT'            => $request->site_date_format,
                'TIME_FORMAT'            => $request->site_time_format,
                'NON_PURCHASE_QUANTITY'  => $request->site_non_purchase_product_maximum_quantity
            ]);

            Artisan::call('optimize:clear');
            return $this->list();
        } catch (Exception $exception) {
            Log::info($exception->getMessage());
            throw new Exception(QueryExceptionLibrary::message($exception), 422);
        }
    }
}
