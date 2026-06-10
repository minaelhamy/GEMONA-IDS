<?php

namespace App\Http\Controllers\Frontend;


use App\Http\Controllers\Admin\AdminController;
use App\Http\Requests\PaginateRequest;
use App\Http\Resources\SimplePaymentGatewayResource;
use App\Services\PaymentGatewayService;
use Exception;


class PaymentGatewayController extends AdminController
{
    private PaymentGatewayService $paymentGatewayService;

    public function __construct(PaymentGatewayService $paymentGatewayService)
    {
        parent::__construct();
        $this->paymentGatewayService = $paymentGatewayService;
    }

    public function index(PaginateRequest $request): \Illuminate\Http\Response|\Illuminate\Http\Resources\Json\AnonymousResourceCollection|\Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\Routing\ResponseFactory
    {
        try {
            return SimplePaymentGatewayResource::collection($this->paymentGatewayService->list($request));
        } catch (Exception $exception) {
            return response(['status' => false, 'message' => $exception->getMessage()], 422);
        }
    }
}
