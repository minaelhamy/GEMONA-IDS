<?php

namespace App\Http\Controllers\Admin;

use Exception;
use App\Models\ProductAttribute;
use App\Http\Requests\PaginateRequest;
use App\Models\ProductAttributeOption;
use Illuminate\Routing\Controllers\Middleware;
use App\Services\ProductAttributeOptionService;
use Illuminate\Routing\Controllers\HasMiddleware;
use App\Http\Requests\ProductAttributeOptionRequest;
use App\Http\Resources\ProductAttributeOptionResource;

class ProductAttributeOptionController extends AdminController implements HasMiddleware
{
    private ProductAttributeOptionService $productAttributeOptionService;

    public function __construct(ProductAttributeOptionService $productAttributeOptionService)
    {
        parent::__construct();
        $this->productAttributeOptionService = $productAttributeOptionService;
    }

    public static function middleware(): array
    {
        return [
            new Middleware('permission:settings', only: ['index', 'store', 'update', 'destroy', 'show']),
        ];
    }

    public function index(PaginateRequest $request, ProductAttribute $productAttribute): \Illuminate\Http\Response | \Illuminate\Http\Resources\Json\AnonymousResourceCollection | \Illuminate\Contracts\Foundation\Application | \Illuminate\Contracts\Routing\ResponseFactory
    {
        try {
            return ProductAttributeOptionResource::collection($this->productAttributeOptionService->list($request, $productAttribute));
        } catch (Exception $exception) {
            return response(['status' => false, 'message' => $exception->getMessage()], 422);
        }
    }

    public function store(ProductAttributeOptionRequest $request, ProductAttribute $productAttribute): \Illuminate\Http\Response | ProductAttributeOptionResource | \Illuminate\Contracts\Foundation\Application | \Illuminate\Contracts\Routing\ResponseFactory
    {
        try {
            return new ProductAttributeOptionResource($this->productAttributeOptionService->store($request, $productAttribute));
        } catch (Exception $exception) {
            return response(['status' => false, 'message' => $exception->getMessage()], 422);
        }
    }

    public function update(ProductAttributeOptionRequest $request, ProductAttribute $productAttribute, ProductAttributeOption $productAttributeOption): \Illuminate\Http\Response | ProductAttributeOptionResource | \Illuminate\Contracts\Foundation\Application | \Illuminate\Contracts\Routing\ResponseFactory
    {
        try {
            return new ProductAttributeOptionResource($this->productAttributeOptionService->update($request, $productAttribute, $productAttributeOption));
        } catch (Exception $exception) {
            return response(['status' => false, 'message' => $exception->getMessage()], 422);
        }
    }

    public function destroy(ProductAttribute $productAttribute, ProductAttributeOption $productAttributeOption): \Illuminate\Http\Response | \Illuminate\Contracts\Foundation\Application | \Illuminate\Contracts\Routing\ResponseFactory
    {
        try {
            $this->productAttributeOptionService->destroy($productAttribute, $productAttributeOption);
            return response('', 202);
        } catch (Exception $exception) {
            return response(['status' => false, 'message' => $exception->getMessage()], 422);
        }
    }

    public function show(ProductAttribute $productAttribute, ProductAttributeOption $productAttributeOption): \Illuminate\Http\Response | ProductAttributeOptionResource | \Illuminate\Contracts\Foundation\Application | \Illuminate\Contracts\Routing\ResponseFactory
    {
        try {
            return new ProductAttributeOptionResource($this->productAttributeOptionService->show($productAttribute, $productAttributeOption));
        } catch (Exception $exception) {
            return response(['status' => false, 'message' => $exception->getMessage()], 422);
        }
    }
}
