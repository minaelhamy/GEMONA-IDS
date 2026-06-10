<?php

namespace App\Http\Controllers\Frontend;


use App\Http\Controllers\Admin\AdminController;
use Exception;
use App\Services\OrderAreaService;
use App\Http\Requests\PaginateRequest;
use App\Http\Resources\OrderAreaResource;

class OrderAreaController extends AdminController
{
    private OrderAreaService $orderAreaService;

    public function __construct(OrderAreaService $orderAreaService)
    {
        parent::__construct();
        $this->orderAreaService = $orderAreaService;
    }

    public function index(PaginateRequest $request): \Illuminate\Http\Response | \Illuminate\Http\Resources\Json\AnonymousResourceCollection | \Illuminate\Contracts\Foundation\Application | \Illuminate\Contracts\Routing\ResponseFactory
    {
        try {
            return OrderAreaResource::collection($this->orderAreaService->list($request));
        } catch (Exception $exception) {
            return response(['status' => false, 'message' => $exception->getMessage()], 422);
        }
    }
}
