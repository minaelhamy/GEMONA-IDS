<?php

namespace App\Services;

use App\Http\Requests\SocialMediaRequest;
use App\Libraries\QueryExceptionLibrary;
use Exception;
use Illuminate\Support\Facades\Log;
use Dipokhalder\Settings\Facades\Settings;

class SocialMediaService
{

    /**
     * @throws Exception
     */
    public function list()
    {
        try {
            return Settings::group('social_media')->all();
        } catch (Exception $exception) {
            Log::info($exception->getMessage());
            throw new Exception(QueryExceptionLibrary::message($exception), 422);
        }
    }

    /**
     * @param SocialMediaRequest $request
     * @return
     * @throws Exception
     */
    public function update(SocialMediaRequest $request)
    {
        try {
            Settings::group('social_media')->set($request->validated());
            return $this->list();
        } catch (Exception $exception) {
            Log::info($exception->getMessage());
            throw new Exception(QueryExceptionLibrary::message($exception), 422);
        }
    }
}
