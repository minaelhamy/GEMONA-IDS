<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use Exception;
use App\Services\ProductBrandService;
use App\Http\Requests\PaginateRequest;
use App\Http\Resources\ProductBrandResource;

class ProductBrandController extends Controller
{
    private ProductBrandService $productBrandService;

    public function __construct(ProductBrandService $productBrandService)
    {
        $this->productBrandService = $productBrandService;
    }

    public function index(PaginateRequest $request): \Illuminate\Http\Response | \Illuminate\Http\Resources\Json\AnonymousResourceCollection | \Illuminate\Contracts\Foundation\Application | \Illuminate\Contracts\Routing\ResponseFactory
    {
        try {
            return ProductBrandResource::collection($this->productBrandService->list($request));
        } catch (Exception $exception) {
            return response(['status' => false, 'message' => $exception->getMessage()], 422);
        }
    }
}
