<?php

namespace App\Http\Controllers\Admin;


use Exception;
use App\Models\Product;
use Illuminate\Http\Request;
use App\Models\ProductVariation;
use App\Http\Requests\PaginateRequest;
use App\Services\ProductVariationService;
use App\Http\Requests\ProductVariationRequest;
use Illuminate\Routing\Controllers\Middleware;
use App\Http\Resources\ProductVariationResource;
use Illuminate\Routing\Controllers\HasMiddleware;
use App\Http\Resources\SimpleProductVariationResource;

class ProductVariationController extends AdminController implements HasMiddleware
{
    private ProductVariationService $productVariationService;
    public function __construct(ProductVariationService $productVariationService)
    {
        parent::__construct();
        $this->productVariationService = $productVariationService;
    }

    public static function middleware(): array
    {
        return [
            new Middleware('permission:products_show', only: ['store', 'update', 'destroy', 'show']),
        ];
    }

    public function tree(Request $request, Product $product): \Illuminate\Http\Response | \Illuminate\Contracts\Foundation\Application | \Illuminate\Contracts\Routing\ResponseFactory
    {
        return response(['data' =>  $this->productVariationService->tree($request, $product)]);
    }

    public function singleTree(Product $product): \Illuminate\Http\Response | \Illuminate\Contracts\Foundation\Application | \Illuminate\Contracts\Routing\ResponseFactory
    {
        return response(['data' =>  $this->productVariationService->singleTree($product)]);
    }

    public function treeWithSelected(Request $request, Product $product): \Illuminate\Http\Response | \Illuminate\Contracts\Foundation\Application | \Illuminate\Contracts\Routing\ResponseFactory
    {
        return response(['data' =>  $this->productVariationService->treeWithSelected($request, $product)]);
    }

    public function index(PaginateRequest $request, Product $product): \Illuminate\Http\Response | \Illuminate\Http\Resources\Json\AnonymousResourceCollection | \Illuminate\Contracts\Foundation\Application | \Illuminate\Contracts\Routing\ResponseFactory
    {
        try {
            return ProductVariationResource::collection($this->productVariationService->list($request, $product));
        } catch (Exception $exception) {
            return response(['status' => false, 'message' => $exception->getMessage()], 422);
        }
    }

    public function store(ProductVariationRequest $request, Product $product): \Illuminate\Http\Response | \Illuminate\Http\Resources\Json\AnonymousResourceCollection | \Illuminate\Contracts\Foundation\Application | \Illuminate\Contracts\Routing\ResponseFactory
    {
        try {
            return ProductVariationResource::collection($this->productVariationService->store($request, $product));
        } catch (Exception $exception) {
            return response(['status' => false, 'message' => $exception->getMessage()], 422);
        }
    }

    public function update(ProductVariationRequest $request, Product $product, ProductVariation $productVariation): \Illuminate\Foundation\Application|\Illuminate\Http\Response|\Illuminate\Http\Resources\Json\AnonymousResourceCollection|\Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\Routing\ResponseFactory
    {
        try {
            return ProductVariationResource::collection($this->productVariationService->update($request, $product, $productVariation));
        } catch (Exception $exception) {
            return response(['status' => false, 'message' => $exception->getMessage()], 422);
        }
    }

    public function destroy(Product $product, ProductVariation $productVariation): \Illuminate\Http\Response | \Illuminate\Contracts\Foundation\Application | \Illuminate\Contracts\Routing\ResponseFactory
    {
        try {
            $this->productVariationService->destroy($product, $productVariation);
            return response('', 202);
        } catch (Exception $exception) {
            return response(['status' => false, 'message' => $exception->getMessage()], 422);
        }
    }

    public function show(Product $product, ProductVariation $productVariation): \Illuminate\Http\Response | ProductVariationResource | \Illuminate\Contracts\Foundation\Application | \Illuminate\Contracts\Routing\ResponseFactory
    {
        try {
            return new ProductVariationResource($this->productVariationService->show($product, $productVariation));
        } catch (Exception $exception) {
            return response(['status' => false, 'message' => $exception->getMessage()], 422);
        }
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

    public function ancestorsAndSelfId(ProductVariation $productVariation): \Illuminate\Foundation\Application|\Illuminate\Http\Response|\Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\Routing\ResponseFactory
    {
        try {
            return response(['data' => $this->productVariationService->ancestorsAndSelfId($productVariation)], 200);
        } catch (Exception $exception) {
            return response(['status' => false, 'message' => $exception->getMessage()], 422);
        }
    }

    public function barcodeVariationProduct(ProductVariation $productVariation): \Illuminate\Http\Response | SimpleProductVariationResource | \Illuminate\Contracts\Foundation\Application | \Illuminate\Contracts\Routing\ResponseFactory
    {
        try {
            return new SimpleProductVariationResource($this->productVariationService->barcodeVariationProduct($productVariation));
        } catch (Exception $exception) {
            return response(['status' => false, 'message' => $exception->getMessage()], 422);
        }
    }

    public function downloadBarcode(ProductVariation $productVariation)
    {
        try {
            return $this->productVariationService->downloadBarcode($productVariation);
        } catch (Exception $exception) {
            return response(['status' => false, 'message' => $exception->getMessage()], 422);
        }
    }
}
