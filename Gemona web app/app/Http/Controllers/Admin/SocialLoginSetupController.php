<?php

namespace App\Http\Controllers\Admin;


use Exception;
use Illuminate\Http\Request;
use App\Http\Resources\SocialLoginSetupResource;
use App\Services\SocialLoginSetupService;
use App\Http\Requests\PaginateRequest;
use Illuminate\Routing\Controllers\Middleware;
use Illuminate\Routing\Controllers\HasMiddleware;

class SocialLoginSetupController extends AdminController implements HasMiddleware
{

    private SocialLoginSetupService $socialLoginSetupService;

    public function __construct(SocialLoginSetupService $socialLoginSetupService)
    {
        parent::__construct();
        $this->socialLoginSetupService = $socialLoginSetupService;
    }

    public static function middleware(): array
    {
        return [
            new Middleware('permission:settings', only: ['index', 'update']),
        ];
    }

    public function index(PaginateRequest $request): \Illuminate\Http\Response|\Illuminate\Http\Resources\Json\AnonymousResourceCollection|\Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\Routing\ResponseFactory
    {
        try {
            return SocialLoginSetupResource::collection($this->socialLoginSetupService->list($request));
        } catch (Exception $exception) {
            return response(['status' => false, 'message' => $exception->getMessage()], 422);
        }
    }

    public function update(Request $request): \Illuminate\Http\Response|SocialLoginSetupResource|\Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\Routing\ResponseFactory
    {
        $className          = 'App\\Http\\SocialProviders\\Requests\\' . ucfirst($request->provider_type);
        $provider            = new $className;
        $validationRequests = $request->validate($provider->rules());
        try {
            return new SocialLoginSetupResource($this->socialLoginSetupService->update($validationRequests));
        } catch (Exception $exception) {
            return response(['status' => false, 'message' => $exception->getMessage()], 422);
        }
    }
}
