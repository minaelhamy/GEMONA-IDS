<?php

namespace App\Http\Controllers\Admin;

use Exception;
use App\Services\OtpService;
use App\Http\Requests\OtpRequest;
use App\Http\Resources\OtpResource;
use Illuminate\Routing\Controllers\Middleware;
use Illuminate\Routing\Controllers\HasMiddleware;

class OtpController extends AdminController implements HasMiddleware
{
    private OtpService $otpService;

    public function __construct(OtpService $otpService)
    {
        parent::__construct();
        $this->otpService = $otpService;
    }

    public static function middleware(): array
    {
        return [
            new Middleware('permission:settings', only: ['index', 'update']),
        ];
    }

    public function index(): \Illuminate\Http\Response|OtpResource|\Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\Routing\ResponseFactory
    {
        try {
            return new OtpResource($this->otpService->list());
        } catch (Exception $exception) {
            return response(['status' => false, 'message' => $exception->getMessage()], 422);
        }
    }

    public function update(OtpRequest $request): \Illuminate\Foundation\Application|\Illuminate\Http\Response|OtpResource|\Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\Routing\ResponseFactory
    {
        try {
            return new OtpResource($this->otpService->update($request));
        } catch (Exception $exception) {
            return response(['status' => false, 'message' => $exception->getMessage()], 422);
        }
    }
}
