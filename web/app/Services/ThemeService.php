<?php

namespace App\Services;


use App\Http\Requests\ThemeRequest;
use App\Libraries\QueryExceptionLibrary;
use App\Models\ThemeSetting;
use Dipokhalder\EnvEditor\EnvEditor;
use Exception;
use Illuminate\Support\Facades\Log;
use Dipokhalder\Settings\Facades\Settings;

class ThemeService
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
            return Settings::group('theme')->all();
        } catch (Exception $exception) {
            Log::info($exception->getMessage());
            throw new Exception(QueryExceptionLibrary::message($exception), 422);
        }
    }

    /**
     * @throws Exception
     */
    public function update(ThemeRequest $request)
    {

        try {
            Settings::group('theme')->set($request->validated());
            if ($request->theme_logo) {
                $setting = ThemeSetting::where('key', 'theme_logo')->first();
                $setting->clearMediaCollection('theme-logo');
                $setting->addMediaFromRequest('theme_logo')->toMediaCollection('theme-logo');
            }
            if ($request->theme_favicon_logo) {
                $setting = ThemeSetting::where('key', 'theme_favicon_logo')->first();
                $setting->clearMediaCollection('theme-favicon-logo');
                $setting->addMediaFromRequest('theme_favicon_logo')->toMediaCollection('theme-favicon-logo');
            }
            if ($request->theme_footer_logo) {
                $setting = ThemeSetting::where('key', 'theme_footer_logo')->first();
                $setting->clearMediaCollection('theme-footer-logo');
                $setting->addMediaFromRequest('theme_footer_logo')->toMediaCollection('theme-footer-logo');
            }
            return $this->list();
        } catch (Exception $exception) {
            Log::info($exception->getMessage());
            throw new Exception(QueryExceptionLibrary::message($exception), 422);
        }
    }
}
