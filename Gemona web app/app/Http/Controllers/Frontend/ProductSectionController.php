<?php

namespace App\Http\Controllers\Frontend;


use App\Http\Controllers\Admin\AdminController;
use App\Http\Resources\ProductSectionDetailsResource;
use App\Http\Resources\ProductSectionResource;
use App\Models\ProductSection;
use Exception;
use App\Http\Requests\PaginateRequest;
use App\Services\ProductSectionService;

class ProductSectionController extends AdminController
{
    private ProductSectionService $productSectionService;

    public function __construct(ProductSectionService $productSectionService)
    {
        parent::__construct();
        $this->productSectionService = $productSectionService;
    }

    public function index(PaginateRequest $request): \Illuminate\Foundation\Application|\Illuminate\Http\Response|\Illuminate\Http\Resources\Json\AnonymousResourceCollection|\Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\Routing\ResponseFactory
    {
        try {
            return ProductSectionDetailsResource::collection($this->productSectionService->productSectionWithProduct($request));
        } catch (Exception $exception) {
            return response(['status' => false, 'message' => $exception->getMessage()], 422);
        }
    }

    public function show(ProductSection $productSection): \Illuminate\Foundation\Application|\Illuminate\Http\Response|ProductSectionResource|\Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\Routing\ResponseFactory
    {
        try {
            return new ProductSectionResource($this->productSectionService->show($productSection));
        } catch (Exception $exception) {
            return response(['status' => false, 'message' => $exception->getMessage()], 422);
        }
    }
}
