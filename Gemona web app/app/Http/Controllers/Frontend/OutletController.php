<?php

namespace App\Http\Controllers\Frontend;

use Exception;
use App\Http\Controllers\Admin\AdminController;
use App\Services\OutletService;
use App\Http\Requests\PaginateRequest;
use App\Http\Resources\OutletResource;


class OutletController extends AdminController
{

    public OutletService $outletService;

    public function __construct(OutletService $outlet)
    {
        parent::__construct();
        $this->outletService = $outlet;
    }

    public function index(PaginateRequest $request): \Illuminate\Foundation\Application|\Illuminate\Http\Response|\Illuminate\Http\Resources\Json\AnonymousResourceCollection|\Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\Routing\ResponseFactory
    {
        try {
            return OutletResource::collection($this->outletService->list($request));
        } catch (Exception $exception) {
            return response(['status' => false, 'message' => $exception->getMessage()], 422);
        }
    }
}
