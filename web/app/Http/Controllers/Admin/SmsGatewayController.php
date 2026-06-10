<?php

namespace App\Http\Controllers\Admin;


use Exception;
use Illuminate\Http\Request;
use App\Services\SmsGatewayService;
use App\Http\Requests\PaginateRequest;
use App\Http\Resources\SmsGatewayResource;
use Illuminate\Routing\Controllers\Middleware;
use Illuminate\Routing\Controllers\HasMiddleware;

class SmsGatewayController extends AdminController implements HasMiddleware
{

    private SmsGatewayService $smsGatewayService;

    public function __construct(SmsGatewayService $smsGatewayService)
    {
        parent::__construct();
        $this->smsGatewayService = $smsGatewayService;
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
            return SmsGatewayResource::collection($this->smsGatewayService->list($request));
        } catch (Exception $exception) {
            return response(['status' => false, 'message' => $exception->getMessage()], 422);
        }
    }

    public function update(Request $request): \Illuminate\Http\Response|SmsGatewayResource|\Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\Routing\ResponseFactory
    {
        $className          = 'App\\Http\\SmsGateways\\Requests\\' . ucfirst($request->sms_type);
        $gateway            = new $className;
        $validationRequests = $request->validate($gateway->rules());
        try {
            return new SmsGatewayResource($this->smsGatewayService->update($validationRequests));
        } catch (Exception $exception) {
            return response(['status' => false, 'message' => $exception->getMessage()], 422);
        }
    }
}
