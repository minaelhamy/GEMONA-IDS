<?php

namespace App\Http\Controllers\Auth;

use Exception;
use App\Http\Requests\PaginateRequest;
use Illuminate\Http\JsonResponse;
use App\Http\Controllers\Controller;
use App\Services\SocialLoginSetupService;
use App\Services\SocialLoginManagerService;
use App\Http\Resources\SimpleSocialLoginResource;

class SocialLoginController extends Controller
{
    /**
     * The social login setup service instance.
     */
    protected $socialLoginSetupService;
    protected $socialLoginManagerService;

    /**
     * Create a new controller instance.
     */
    public function __construct(SocialLoginSetupService $socialLoginSetupService, SocialLoginManagerService $socialLoginManagerService)
    {
        $this->socialLoginSetupService = $socialLoginSetupService;
        $this->socialLoginManagerService = $socialLoginManagerService;
    }
    
    public function index(PaginateRequest $request)
    {
        try {
            return SimpleSocialLoginResource::collection($this->socialLoginSetupService->list($request));
        } catch (Exception $exception) {
            return response(['status' => false, 'message' => $exception->getMessage()], 422);
        }
    }

    public function socialLogin($provider): JsonResponse
    {
          
        return $this->socialLoginManagerService->provider($provider)->getUrl();
    }

    public function verifySocialLogin($provider)
    {
        return $this->socialLoginManagerService->provider($provider)->verifySocialLogin();
      
    }
}
