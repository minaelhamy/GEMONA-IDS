<?php

namespace App\Http\Controllers\Frontend;

use Exception;
use App\Models\ReturnAndRefund;
use App\Http\Controllers\Controller;
use App\Http\Requests\PaginateRequest;
use App\Services\ReturnAndRefundService;
use App\Http\Requests\ReturnAndRefundRequest;
use App\Http\Resources\ReturnAndRefundResource;
use App\Http\Resources\ReturnAndRefundDetailsResource;

class ReturnAndRefundController extends Controller
{
    private ReturnAndRefundService $returnAndRefundService;

    public function __construct(ReturnAndRefundService $returnAndRefundService)
    {
        $this->returnAndRefundService = $returnAndRefundService;
    }

    public function index(
        PaginateRequest $request
    ): \Illuminate\Http\Response | \Illuminate\Http\Resources\Json\AnonymousResourceCollection | \Illuminate\Contracts\Foundation\Application | \Illuminate\Contracts\Routing\ResponseFactory {
        try {
            return ReturnAndRefundResource::collection($this->returnAndRefundService->list($request, true));
        } catch (Exception $exception) {
            return response(['status' => false, 'message' => $exception->getMessage()], 422);
        }
    }

    public function store(ReturnAndRefundRequest $request): \Illuminate\Http\Response | ReturnAndRefundResource | \Illuminate\Contracts\Foundation\Application | \Illuminate\Contracts\Routing\ResponseFactory
    {
        try {
            return new ReturnAndRefundResource($this->returnAndRefundService->store($request));
        } catch (Exception $exception) {
            return response(['status' => false, 'message' => $exception->getMessage()], 422);
        }
    }

    public function show(ReturnAndRefund $returnAndRefund): \Illuminate\Foundation\Application|\Illuminate\Http\Response|ReturnAndRefundDetailsResource|\Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\Routing\ResponseFactory
    {
        try {
            return new ReturnAndRefundDetailsResource($this->returnAndRefundService->show($returnAndRefund, true));
        } catch (Exception $exception) {
            return response(['status' => false, 'message' => $exception->getMessage()], 422);
        }
    }
}
