<?php

namespace App\Http\Controllers\Admin;

use Exception;
use App\Services\CookiesService;
use App\Http\Requests\CookiesRequest;
use App\Http\Resources\CookiesResource;
use Illuminate\Routing\Controllers\Middleware;
use Illuminate\Routing\Controllers\HasMiddleware;

class CookiesController extends AdminController implements HasMiddleware
{
    private CookiesService $cookiesService;

    public function __construct(CookiesService $cookiesService)
    {
        parent::__construct();
        $this->cookiesService = $cookiesService;
    }

    public static function middleware(): array
    {
        return [
            new Middleware('permission:settings', only: ['index', 'update']),
        ];
    }

    public function index(): \Illuminate\Http\Response|CookiesResource|\Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\Routing\ResponseFactory
    {
        try {
            return new CookiesResource($this->cookiesService->list());
        } catch (Exception $exception) {
            return response(['status' => false, 'message' => $exception->getMessage()], 422);
        }
    }

    public function update(CookiesRequest $request): \Illuminate\Http\Response|CookiesResource|\Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\Routing\ResponseFactory
    {
        try {
            return new CookiesResource($this->cookiesService->update($request));
        } catch (Exception $exception) {
            return response(['status' => false, 'message' => $exception->getMessage()], 422);
        }
    }
}
