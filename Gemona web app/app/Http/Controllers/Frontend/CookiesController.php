<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Requests\CookiesSetRequest;
use App\Http\Resources\CookiesSetResource;
use App\Services\CookiesService;
use Exception;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class CookiesController extends Controller
{
    public CookiesService $cookiesService;

    public function __construct(CookiesService $cookiesService)
    {
        $this->cookiesService = $cookiesService;
    }

    public function get(Request $request) : \Illuminate\Http\Response | \Illuminate\Contracts\Foundation\Application | \Illuminate\Contracts\Routing\ResponseFactory | CookiesSetResource
    {
        try {
            return new CookiesSetResource($this->cookiesService->get($request));
        } catch (Exception $exception) {
            return response(['status' => false, 'message' => $exception->getMessage()], 422);
        }
    }

    public function set(CookiesSetRequest $cookiesSetRequest) : \Illuminate\Http\Response | \Illuminate\Contracts\Foundation\Application | \Illuminate\Contracts\Routing\ResponseFactory | CookiesSetResource
    {
        try {
            return new CookiesSetResource($this->cookiesService->set($cookiesSetRequest));
        } catch (Exception $exception) {
            return response(['status' => false, 'message' => $exception->getMessage()], 422);
        }
    }
}
