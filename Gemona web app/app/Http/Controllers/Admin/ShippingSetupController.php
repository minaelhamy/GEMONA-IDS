<?php

namespace App\Http\Controllers\Admin;

use Exception;
use App\Services\ShippingSetupService;
use App\Http\Requests\ShippingSetupRequest;
use App\Http\Resources\ShippingSetupResource;
use Illuminate\Routing\Controllers\Middleware;
use Illuminate\Routing\Controllers\HasMiddleware;

class ShippingSetupController extends AdminController implements HasMiddleware
{
    public ShippingSetupService $shippingSetupService;

    public function __construct(ShippingSetupService $shippingSetupService)
    {
        parent::__construct();
        $this->shippingSetupService = $shippingSetupService;
    }

    public static function middleware(): array
    {
        return [
            new Middleware('permission:settings', only: ['index', 'update']),
        ];
    }

    public function index(): \Illuminate\Http\Response | ShippingSetupResource | \Illuminate\Contracts\Foundation\Application | \Illuminate\Contracts\Routing\ResponseFactory
    {
        try {
            return new ShippingSetupResource($this->shippingSetupService->list());
        } catch (Exception $exception) {
            return response(['status' => false, 'message' => $exception->getMessage()], 422);
        }
    }

    public function update(
        ShippingSetupRequest $request
    ): \Illuminate\Http\Response | ShippingSetupResource | \Illuminate\Contracts\Foundation\Application | \Illuminate\Contracts\Routing\ResponseFactory {
        try {
            return new ShippingSetupResource($this->shippingSetupService->update($request));
        } catch (Exception $exception) {
            return response(['status' => false, 'message' => $exception->getMessage()], 422);
        }
    }
}