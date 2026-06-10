<?php

namespace App\Http\Controllers\Frontend;


use App\Http\Controllers\Controller;
use App\Http\Resources\SimpleProductResource;
use App\Models\ProductSection;
use App\Services\ProductSectionProductService;
use Exception;
use App\Http\Requests\PaginateRequest;

class ProductSectionProductController extends Controller
{
    public ProductSectionProductService $productSectionProductService;

    public function __construct(ProductSectionProductService $productSectionProductService)
    {
        $this->productSectionProductService = $productSectionProductService;
    }

    public function index(PaginateRequest $request, ProductSection $productSection): \Illuminate\Foundation\Application|\Illuminate\Http\Response|\Illuminate\Http\Resources\Json\AnonymousResourceCollection|\Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\Routing\ResponseFactory
    {
        try {
            return SimpleProductResource::collection($this->productSectionProductService->productWithPagination($request, $productSection));
        } catch (Exception $exception) {
            return response(['status' => false, 'message' => $exception->getMessage()], 422);
        }
    }
}
