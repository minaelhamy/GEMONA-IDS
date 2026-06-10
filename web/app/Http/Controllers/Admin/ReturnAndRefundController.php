<?php

namespace App\Http\Controllers\Admin;

use Exception;
use App\Models\ReturnAndRefund;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\ReturnAndRefundExport;
use App\Http\Requests\PaginateRequest;
use App\Services\ReturnAndRefundService;
use App\Http\Requests\OrderStatusRequest;
use Illuminate\Routing\Controllers\Middleware;
use App\Http\Resources\ReturnAndRefundResource;
use Illuminate\Routing\Controllers\HasMiddleware;
use App\Http\Resources\ReturnAndRefundDetailsResource;

class ReturnAndRefundController extends AdminController implements HasMiddleware
{
    private ReturnAndRefundService $returnAndRefundService;

    public function __construct(ReturnAndRefundService $returnAndRefundService)
    {
        parent::__construct();
        $this->returnAndRefundService = $returnAndRefundService;
    }

    public static function middleware(): array
    {
        return [
            new Middleware('permission:return-and-refunds', only: ['index']),
            new Middleware('permission:return-and-refunds', only: ['export']),
            new Middleware('permission:return-and-refunds', only: ['show']),
            new Middleware('permission:return-and-refunds', only: ['changeStatus']),
        ];
    }

    public function index(PaginateRequest $request): \Illuminate\Http\Response|\Illuminate\Http\Resources\Json\AnonymousResourceCollection|\Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\Routing\ResponseFactory
    {
        try {
            return ReturnAndRefundResource::collection($this->returnAndRefundService->list($request, false));
        } catch (Exception $exception) {
            return response(['status' => false, 'message' => $exception->getMessage()], 422);
        }
    }

    public function show(ReturnAndRefund $returnAndRefund): \Illuminate\Foundation\Application|\Illuminate\Http\Response|ReturnAndRefundDetailsResource|\Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\Routing\ResponseFactory
    {
        try {
            return new ReturnAndRefundDetailsResource($this->returnAndRefundService->show($returnAndRefund, false));
        } catch (Exception $exception) {
            return response(['status' => false, 'message' => $exception->getMessage()], 422);
        }
    }

    public function changeStatus(ReturnAndRefund $returnAndRefund, OrderStatusRequest $request): \Illuminate\Http\Response|ReturnAndRefundDetailsResource|\Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\Routing\ResponseFactory
    {
        try {
            return new ReturnAndRefundDetailsResource($this->returnAndRefundService->changeStatus($returnAndRefund, $request));
        } catch (Exception $exception) {
            return response(['status' => false, 'message' => $exception->getMessage()], 422);
        }
    }

    public function export(PaginateRequest $request): \Illuminate\Http\Response|\Symfony\Component\HttpFoundation\BinaryFileResponse|\Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\Routing\ResponseFactory
    {
        try {
            return Excel::download(new ReturnAndRefundExport($this->returnAndRefundService, $request), 'Return-Order.xlsx');
        } catch (Exception $exception) {
            return response(['status' => false, 'message' => $exception->getMessage()], 422);
        }
    }
}
