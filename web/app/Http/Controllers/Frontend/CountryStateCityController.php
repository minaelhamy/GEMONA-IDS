<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Http\Resources\CityResource;
use App\Http\Resources\CountryResource;
use App\Http\Resources\StateResource;
use App\Models\Country;
use App\Models\State;
use App\Services\CountryStateCityService;
use Exception;
use Illuminate\Support\Facades\Log;

class CountryStateCityController extends Controller
{
    public CountryStateCityService $countryStateCityService;
    public function __construct(CountryStateCityService $countryStateCityService)
    {
        $this->countryStateCityService = $countryStateCityService;
    }

    public function countries()
    {
        try {
            return CountryResource::collection($this->countryStateCityService->countries());
        } catch (Exception $exception) {
            Log::info($exception->getMessage());
            throw new Exception($exception->getMessage(), 422);
        }
    }

    public function statesByCountry($countryName)
    {
        try {
            return StateResource::collection($this->countryStateCityService->statesByCountry($countryName));
        } catch (Exception $exception) {
            Log::info($exception->getMessage());
            throw new Exception($exception->getMessage(), 422);
        }
    }

    public function citiesByState($stateName)
    {
        try {
            return CityResource::collection($this->countryStateCityService->citiesByState($stateName));
        } catch (Exception $exception) {
            Log::info($exception->getMessage());
            throw new Exception($exception->getMessage(), 422);
        }
    }
}
