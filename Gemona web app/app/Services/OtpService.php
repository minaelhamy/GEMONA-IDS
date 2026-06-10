<?php

namespace App\Services;

use App\Http\Requests\OtpRequest;
use App\Libraries\QueryExceptionLibrary;
use Exception;
use Illuminate\Support\Facades\Log;
use Dipokhalder\Settings\Facades\Settings;

class OtpService
{

    /**
     * @throws Exception
     */
    public function list()
    {
        try {
            return Settings::group('otp')->all();
        } catch (Exception $exception) {
            Log::info($exception->getMessage());
            throw new Exception(QueryExceptionLibrary::message($exception), 422);
        }
    }

    /**
     * @param OtpRequest $request
     * @return
     * @throws Exception
     */
    public function update(OtpRequest $request)
    {
        try {
            Settings::group('otp')->set($request->validated());
            return $this->list();
        } catch (Exception $exception) {
            Log::info($exception->getMessage());
            throw new Exception(QueryExceptionLibrary::message($exception), 422);
        }
    }
}
