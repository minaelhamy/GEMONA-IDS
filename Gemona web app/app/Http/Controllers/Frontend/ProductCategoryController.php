<?php

namespace App\Http\Controllers\Frontend;


use Exception;
use App\Models\ProductCategory;
use App\Http\Controllers\Controller;
use App\Http\Requests\PaginateRequest;
use App\Services\ProductCategoryService;
use App\Http\Resources\ProductCategoryResource;


class ProductCategoryController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    private ProductCategoryService $productCategoryService;

    public function __construct(ProductCategoryService $productCategoryService)
    {
        $this->productCategoryService = $productCategoryService;
    }


    public function ancestorsAndSelf(ProductCategory $productCategory): \Illuminate\Foundation\Application|\Illuminate\Http\Response|\Illuminate\Http\Resources\Json\AnonymousResourceCollection|\Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\Routing\ResponseFactory
    {
        try {
            return ProductCategoryResource::collection($this->productCategoryService->ancestorsAndSelf($productCategory));
        } catch (Exception $exception) {
            return response(['status' => false, 'message' => $exception->getMessage()], 422);
        }
    }

    public function tree()
    {
        try {
            return $this->productCategoryService->tree()->toTree();
        } catch (Exception $exception) {
            return response(['status' => false, 'message' => $exception->getMessage()], 422);
        }
    }


    public function index(PaginateRequest $request): \Illuminate\Http\Response | \Illuminate\Http\Resources\Json\AnonymousResourceCollection | \Illuminate\Contracts\Foundation\Application | \Illuminate\Contracts\Routing\ResponseFactory
    {
        try {
            return ProductCategoryResource::collection($this->productCategoryService->list($request));
        } catch (Exception $exception) {
            return response(['status' => false, 'message' => $exception->getMessage()], 422);
        }
    }

    public function show(ProductCategory $productCategory): \Illuminate\Foundation\Application|\Illuminate\Http\Response|ProductCategoryResource|\Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\Routing\ResponseFactory
    {
        try {
            return new ProductCategoryResource($this->productCategoryService->show($productCategory));
        } catch (Exception $exception) {
            return response(['status' => false, 'message' => $exception->getMessage()], 422);
        }
    }
}
