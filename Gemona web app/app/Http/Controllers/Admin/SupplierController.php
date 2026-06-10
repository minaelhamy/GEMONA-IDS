<?php

namespace App\Http\Controllers\Admin;

use Exception;
use App\Models\Supplier;
use App\Services\SupplierService;
use App\Http\Requests\PaginateRequest;
use App\Http\Requests\SupplierRequest;
use App\Http\Resources\SupplierResource;
use Illuminate\Routing\Controllers\Middleware;
use Illuminate\Routing\Controllers\HasMiddleware;

class SupplierController extends AdminController implements HasMiddleware
{
    private SupplierService $supplierService;

    public function __construct(SupplierService $supplierService)
    {
        parent::__construct();
        $this->supplierService = $supplierService;
    }

    public static function middleware(): array
    {
        return [
            new Middleware('permission:settings|purchase', only: ['index']),
            new Middleware('permission:settings', only: ['store', 'update', 'destroy', 'show']),
        ];
    }

    public function index(PaginateRequest $request): \Illuminate\Http\Response | \Illuminate\Http\Resources\Json\AnonymousResourceCollection | \Illuminate\Contracts\Foundation\Application | \Illuminate\Contracts\Routing\ResponseFactory
    {
        try {
            return SupplierResource::collection($this->supplierService->list($request));
        } catch (Exception $exception) {
            return response(['status' => false, 'message' => $exception->getMessage()], 422);
        }
    }

    public function store(SupplierRequest $request): \Illuminate\Http\Response | SupplierResource | \Illuminate\Contracts\Foundation\Application | \Illuminate\Contracts\Routing\ResponseFactory
    {
        try {
            return new SupplierResource($this->supplierService->store($request));
        } catch (Exception $exception) {
            return response(['status' => false, 'message' => $exception->getMessage()], 422);
        }
    }

    public function update(SupplierRequest $request, Supplier $supplier): \Illuminate\Http\Response | SupplierResource | \Illuminate\Contracts\Foundation\Application | \Illuminate\Contracts\Routing\ResponseFactory
    {
        try {
            return new SupplierResource($this->supplierService->update($request, $supplier));
        } catch (Exception $exception) {
            return response(['status' => false, 'message' => $exception->getMessage()], 422);
        }
    }

    public function destroy(Supplier $supplier): \Illuminate\Http\Response | \Illuminate\Contracts\Foundation\Application | \Illuminate\Contracts\Routing\ResponseFactory
    {
        try {
            $this->supplierService->destroy($supplier);
            return response('', 202);
        } catch (Exception $exception) {
            return response(['status' => false, 'message' => $exception->getMessage()], 422);
        }
    }

    public function show(Supplier $supplier): \Illuminate\Http\Response | SupplierResource | \Illuminate\Contracts\Foundation\Application | \Illuminate\Contracts\Routing\ResponseFactory
    {
        try {
            return new SupplierResource($this->supplierService->show($supplier));
        } catch (Exception $exception) {
            return response(['status' => false, 'message' => $exception->getMessage()], 422);
        }
    }
}
