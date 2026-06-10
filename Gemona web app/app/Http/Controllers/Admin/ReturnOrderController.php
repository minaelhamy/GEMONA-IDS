<?php

namespace App\Http\Controllers\Admin;

use Exception;
use App\Models\ReturnOrder;
use App\Exports\ReturnOrdersExport;
use App\Services\ReturnOrderService;
use Maatwebsite\Excel\Facades\Excel;
use App\Http\Requests\PaginateRequest;
use App\Http\Requests\ReturnOrderRequest;
use App\Http\Resources\ReturnOrderResource;
use Illuminate\Routing\Controllers\Middleware;
use Illuminate\Routing\Controllers\HasMiddleware;
use App\Http\Resources\ReturnOrderDetailsResource;

class ReturnOrderController extends AdminController implements HasMiddleware
{

    public ReturnOrderService $returnOrderService;

    public function __construct(ReturnOrderService $returnOrderService)
    {
        parent::__construct();
        $this->returnOrderService = $returnOrderService;
    }

    public static function middleware(): array
    {
        return [
            new Middleware('permission:return-orders', only: ['index']),
            new Middleware('permission:return-orders', only: ['export']),
            new Middleware('permission:return-orders', only: ['downloadAttachment']),
            new Middleware('permission:return_order_create', only: ['store']),
            new Middleware('permission:return_order_edit', only: ['edit']),
            new Middleware('permission:return_order_edit', only: ['update']),
            new Middleware('permission:return_order_delete', only: ['destroy']),
            new Middleware('permission:return_order_show', only: ['show']),
        ];
    }

    public function index(PaginateRequest $request): \Illuminate\Foundation\Application|\Illuminate\Http\Response|\Illuminate\Http\Resources\Json\AnonymousResourceCollection|\Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\Routing\ResponseFactory
    {
        try {
            return  ReturnOrderResource::collection($this->returnOrderService->list($request));
        } catch (Exception $exception) {
            return response(['status' => false, 'message' => $exception->getMessage()], 422);
        }
    }
    public function store(ReturnOrderRequest $request): \Illuminate\Http\Response|\Illuminate\Foundation\Application|ReturnOrderResource|\Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\Routing\ResponseFactory
    {
        try {
            return new ReturnOrderResource($this->returnOrderService->store($request));
        } catch (Exception $exception) {
            return response(['status' => false, 'message' => $exception->getMessage()], 422);
        }
    }
    public function show(ReturnOrder $returnOrder): \Illuminate\Foundation\Application|\Illuminate\Http\Response|ReturnOrderDetailsResource|\Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\Routing\ResponseFactory
    {
        try {
            return new ReturnOrderDetailsResource($this->returnOrderService->show($returnOrder));
        } catch (Exception $exception) {
            return response(['status' => false, 'message' => $exception->getMessage()], 422);
        }
    }
    public function edit(ReturnOrder $returnOrder): \Illuminate\Foundation\Application|\Illuminate\Http\Response|ReturnOrderDetailsResource|\Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\Routing\ResponseFactory
    {
        try {
            return new ReturnOrderDetailsResource($this->returnOrderService->edit($returnOrder));
        } catch (Exception $exception) {
            return response(['status' => false, 'message' => $exception->getMessage()], 422);
        }
    }
    public function update(ReturnOrderRequest $request, ReturnOrder $returnOrder): \Illuminate\Http\Response|\Illuminate\Foundation\Application|ReturnOrderResource|\Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\Routing\ResponseFactory
    {
        try {
            return new ReturnOrderResource($this->returnOrderService->update($request, $returnOrder));
        } catch (Exception $exception) {
            return response(['status' => false, 'message' => $exception->getMessage()], 422);
        }
    }
    public function destroy(ReturnOrder $returnOrder): \Illuminate\Foundation\Application|\Illuminate\Http\Response|\Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\Routing\ResponseFactory
    {
        try {
            $this->returnOrderService->destroy($returnOrder);
            return response('', 202);
        } catch (Exception $exception) {
            return response(['status' => false, 'message' => $exception->getMessage()], 422);
        }
    }
    public function export(PaginateRequest $request): \Illuminate\Foundation\Application|\Illuminate\Http\Response|\Symfony\Component\HttpFoundation\BinaryFileResponse|\Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\Routing\ResponseFactory
    {
        try {
            return Excel::download(new ReturnOrdersExport($this->returnOrderService, $request), 'ReturnOrders.xlsx');
        } catch (Exception $exception) {
            return response(['status' => false, 'message' => $exception->getMessage()], 422);
        }
    }
    public function downloadAttachment(ReturnOrder $returnOrder)
    {
        try {
            return $this->returnOrderService->downloadAttachment($returnOrder);
        } catch (Exception $exception) {
            return response(['status' => false, 'message' => $exception->getMessage()], 422);
        }
    }
}
