<?php

namespace App\Http\Controllers\Admin;

use Exception;
use App\Models\ProductSection;
use App\Models\ProductSectionProduct;
use App\Http\Requests\PaginateRequest;
use App\Services\ProductSectionProductService;
use Illuminate\Routing\Controllers\Middleware;
use Illuminate\Routing\Controllers\HasMiddleware;
use App\Http\Requests\ProductSectionProductRequest;
use App\Http\Resources\ProductSectionProductResource;

class ProductSectionProductController extends AdminController implements HasMiddleware
{

    public ProductSectionProductService $productSectionProductService;

    public function __construct(ProductSectionProductService $productSectionProductService)
    {
        parent::__construct();
        $this->productSectionProductService = $productSectionProductService;
    }

    public static function middleware(): array
    {
        return [
            new Middleware('permission:product-sections_show', only: ['index', 'store', 'destroy']),
        ];
    }

    public function index(PaginateRequest $request, ProductSection $productSection): \Illuminate\Http\Response|\Illuminate\Http\Resources\Json\AnonymousResourceCollection|\Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\Routing\ResponseFactory
    {
        try {
            return ProductSectionProductResource::collection($this->productSectionProductService->list($request, $productSection));
        } catch (Exception $exception) {
            return response(['status' => false, 'message' => $exception->getMessage()], 422);
        }
    }


    public function store(ProductSectionProductRequest $request, ProductSection $productSection): \Illuminate\Http\Response|ProductSectionProductResource|\Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\Routing\ResponseFactory
    {
        try {
            return new ProductSectionProductResource($this->productSectionProductService->store($request, $productSection));
        } catch (Exception $exception) {
            return response(['status' => false, 'message' => $exception->getMessage()], 422);
        }
    }

    public function destroy(ProductSection $productSection, ProductSectionProduct $productSectionProduct): \Illuminate\Http\Response|\Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\Routing\ResponseFactory
    {
        try {
            $this->productSectionProductService->destroy($productSection, $productSectionProduct);
            return response('', 202);
        } catch (Exception $exception) {
            return response(['status' => false, 'message' => $exception->getMessage()], 422);
        }
    }
}
