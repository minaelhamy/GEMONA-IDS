<?php

namespace App\Services;


use App\Http\Requests\LicenseRequest;
use App\Libraries\QueryExceptionLibrary;
use Dipokhalder\EnvEditor\EnvEditor;
use Exception;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Log;
use Dipokhalder\Settings\Facades\Settings;

class LicenseService
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
            return Settings::group('license')->all();
        } catch (Exception $exception) {
            Log::info($exception->getMessage());
            throw new Exception(QueryExceptionLibrary::message($exception), 422);
        }
    }

    /**
     * @param LicenseRequest $request
     * @return
     * @throws Exception
     */
    public function update(LicenseRequest $request)
    {
        try {
            Settings::group('license')->set($request->validated());
            $this->envService->addData(['VITE_API_KEY' => $request->license_key]);
            Artisan::call('optimize:clear');
            return $this->list();
        } catch (Exception $exception) {
            Log::info($exception->getMessage());
            throw new Exception(QueryExceptionLibrary::message($exception), 422);
        }
    }
}
