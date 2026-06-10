<?php

namespace App\Http\Controllers\Frontend;


use Exception;
use App\Models\Product;
use Illuminate\Http\Request;
use App\Models\ProductVariation;
use App\Http\Controllers\Controller;
use App\Services\ProductVariationService;
use App\Http\Resources\ProductAllVariationResource;
use App\Http\Resources\SimpleProductVariationResource;

class ProductVariationController extends Controller
{
    private ProductVariationService $productVariationService;

    public function __construct(ProductVariationService $productVariationService)
    {
        $this->productVariationService = $productVariationService;
    }

    public function initialVariation(Product $product): \Illuminate\Foundation\Application|\Illuminate\Http\Response|\Illuminate\Http\Resources\Json\AnonymousResourceCollection|\Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\Routing\ResponseFactory
    {
        try {
            return SimpleProductVariationResource::collection($this->productVariationService->initialVariation($product));
        } catch (Exception $exception) {
            return response(['status' => false, 'message' => $exception->getMessage()], 422);
        }
    }

    public function childrenVariation(ProductVariation $productVariation): \Illuminate\Foundation\Application|\Illuminate\Http\Response|\Illuminate\Http\Resources\Json\AnonymousResourceCollection|\Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\Routing\ResponseFactory
    {
        try {
            return SimpleProductVariationResource::collection($this->productVariationService->childrenVariation($productVariation));
        } catch (Exception $exception) {
            return response(['status' => false, 'message' => $exception->getMessage()], 422);
        }
    }

    public function ancestorsToString(ProductVariation $productVariation): \Illuminate\Foundation\Application|\Illuminate\Http\Response|\Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\Routing\ResponseFactory
    {
        try {
            return response(['data' => $this->productVariationService->ancestorsToString($productVariation)], 200);
        } catch (Exception $exception) {
            return response(['status' => false, 'message' => $exception->getMessage()], 422);
        }
    }

    public function allVariation(Product $product)
    {
        try {
            return ProductAllVariationResource::collection($this->productVariationService->allVariation($product));
        } catch (Exception $exception) {
            return response(['status' => false, 'message' => $exception->getMessage()], 422);
        }
    }
}
