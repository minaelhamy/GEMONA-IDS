<?php

namespace App\Http\Controllers\Admin;

use Exception;
use App\Models\Product;
use Illuminate\Http\Request;
use App\Exports\ProductExport;
use App\Imports\ProductImport;
use App\Services\ProductService;
use Maatwebsite\Excel\Facades\Excel;
use App\Http\Requests\ProductRequest;
use App\Http\Requests\PaginateRequest;
use App\Http\Requests\ImportFileRequest;
use Illuminate\Support\Facades\Response;
use App\Http\Requests\ChangeImageRequest;
use App\Http\Requests\ProductOfferRequest;
use App\Http\Resources\ProductAdminResource;
use Illuminate\Routing\Controllers\Middleware;
use App\Http\Requests\ShippingAndReturnRequest;
use Illuminate\Routing\Controllers\HasMiddleware;
use App\Http\Resources\ProductDetailsAdminResource;
use App\Http\Resources\SimpleProductDetailsResource;
use App\Http\Resources\simpleProductWithVariationCountResource;

class ProductController extends AdminController implements HasMiddleware
{
    public ProductService $productService;

    public function __construct(ProductService $productService)
    {
        parent::__construct();
        $this->productService = $productService;
    }

    public static function middleware(): array
    {
        return [
            new Middleware('permission:products|pos|promotions|product-sections', only: ['index']),
            new Middleware('permission:products', only: ['export']),
            new Middleware('permission:products', only: ['generateSku']),
            new Middleware('permission:products', only: ['downloadAttachment']),
            new Middleware('permission:products_create', only: ['store']),
            new Middleware('permission:products_create', only: ['uploadImage']),
            new Middleware('permission:products_create', only: ['import']),
            new Middleware('permission:products_edit', only: ['update']),
            new Middleware('permission:products_delete', only: ['destroy']),
            new Middleware('permission:products_delete', only: ['deleteImage']),
            new Middleware('permission:products_show', only: ['show']),
            new Middleware('permission:products_show', only: ['shippingAndReturn']),
            new Middleware('permission:products_show', only: ['downloadBarcode']),
        ];
    }

    public function index(PaginateRequest $request): \Illuminate\Http\Response|\Illuminate\Http\Resources\Json\AnonymousResourceCollection|\Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\Routing\ResponseFactory
    {
        try {
            return ProductAdminResource::collection($this->productService->list($request));
        } catch (Exception $exception) {
            return response(['status' => false, 'message' => $exception->getMessage()], 422);
        }
    }

    public function show(Product $product): \Illuminate\Foundation\Application|\Illuminate\Http\Response|ProductDetailsAdminResource|\Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\Routing\ResponseFactory
    {
        try {
            return new ProductDetailsAdminResource($this->productService->show($product));
        } catch (Exception $exception) {
            return response(['status' => false, 'message' => $exception->getMessage()], 422);
        }
    }

    public function store(ProductRequest $request): \Illuminate\Http\Response|ProductAdminResource|\Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\Routing\ResponseFactory
    {
        try {
            return new ProductAdminResource($this->productService->store($request));
        } catch (Exception $exception) {
            return response(['status' => false, 'message' => $exception->getMessage()], 422);
        }
    }

    public function update(ProductRequest $request, Product $product): \Illuminate\Http\Response|ProductAdminResource|\Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\Routing\ResponseFactory
    {
        try {
            if (env('DEMO')) {
                return response(['status' => false, 'message' => 'This action is not allowed in demo mode.'], 422);
            }  else {
            return new ProductAdminResource($this->productService->update($request, $product));
            }
        } catch (Exception $exception) {
            return response(['status' => false, 'message' => $exception->getMessage()], 422);
        }
    }

    public function destroy(Product $product): \Illuminate\Http\Response|\Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\Routing\ResponseFactory
    {
        try {
            if (env('DEMO')) {
                return response(['status' => false, 'message' => 'This action is not allowed in demo mode.'], 422);
            }  else {
            $this->productService->destroy($product);
            return response('', 202);
            }
        } catch (Exception $exception) {
            return response(['status' => false, 'message' => $exception->getMessage()], 422);
        }
    }

    public function uploadImage(ChangeImageRequest $request, Product $product): \Illuminate\Foundation\Application|\Illuminate\Http\Response|ProductDetailsAdminResource|\Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\Routing\ResponseFactory
    {
        try {
            return new ProductDetailsAdminResource($this->productService->uploadImage($request, $product));
        } catch (Exception $exception) {
            return response(['status' => false, 'message' => $exception->getMessage()], 422);
        }
    }

    public function deleteImage(Product $product, $index): \Illuminate\Foundation\Application|\Illuminate\Http\Response|ProductDetailsAdminResource|\Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\Routing\ResponseFactory
    {
        try {
            if (env('DEMO')) {
                return response(['status' => false, 'message' => 'This action is not allowed in demo mode.'], 422);
            }  else {
            return new ProductDetailsAdminResource($this->productService->deleteImage($product, $index));
            }
        } catch (Exception $exception) {
            return response(['status' => false, 'message' => $exception->getMessage()], 422);
        }
    }

    public function export(PaginateRequest $request): \Illuminate\Http\Response|\Symfony\Component\HttpFoundation\BinaryFileResponse|\Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\Routing\ResponseFactory
    {
        try {
            return Excel::download(new ProductExport($this->productService, $request), 'Product.xlsx');
        } catch (Exception $exception) {
            return response(['status' => false, 'message' => $exception->getMessage()], 422);
        }
    }

    public function generateSku(): \Illuminate\Foundation\Application|\Illuminate\Http\Response|\Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\Routing\ResponseFactory
    {
        try {
            return response(['data' => ['product_sku' => $this->productService->generateSku()]], 200);
        } catch (Exception $exception) {
            return response(['status' => false, 'message' => $exception->getMessage()], 422);
        }
    }

    public function shippingAndReturn(ShippingAndReturnRequest $request, Product $product): \Illuminate\Foundation\Application|\Illuminate\Http\Response|ProductAdminResource|\Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\Routing\ResponseFactory
    {
        try {
            return new ProductAdminResource($this->productService->shippingAndReturn($request, $product));
        } catch (Exception $exception) {
            return response(['status' => false, 'message' => $exception->getMessage()], 422);
        }
    }
    public function productOffer(ProductOfferRequest $request, Product $product): \Illuminate\Foundation\Application|\Illuminate\Http\Response|ProductAdminResource|\Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\Routing\ResponseFactory
    {
        try {
            return new ProductAdminResource($this->productService->productOffer($request, $product));
        } catch (Exception $exception) {
            return response(['status' => false, 'message' => $exception->getMessage()], 422);
        }
    }
    public function purchasableProducts(): \Illuminate\Foundation\Application|\Illuminate\Http\Response|\Illuminate\Http\Resources\Json\AnonymousResourceCollection|\Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\Routing\ResponseFactory
    {
        try {
            return  simpleProductWithVariationCountResource::collection($this->productService->purchasableProducts());
        } catch (Exception $exception) {
            return response(['status' => false, 'message' => $exception->getMessage()], 422);
        }
    }
    public function simpleProducts(): \Illuminate\Foundation\Application|\Illuminate\Http\Response|\Illuminate\Http\Resources\Json\AnonymousResourceCollection|\Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\Routing\ResponseFactory
    {
        try {
            return  simpleProductWithVariationCountResource::collection($this->productService->simpleProducts());
        } catch (Exception $exception) {
            return response(['status' => false, 'message' => $exception->getMessage()], 422);
        }
    }

    public function posProduct(Product $product, Request $request): SimpleProductDetailsResource|\Illuminate\Foundation\Application|\Illuminate\Http\Response|\Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\Routing\ResponseFactory
    {
        try {
            return new SimpleProductDetailsResource($this->productService->showWithRelation($product, $request));
        } catch (Exception $exception) {
            return response(['status' => false, 'message' => $exception->getMessage()], 422);
        }
    }

    public function downloadAttachment()
    {
        try {
            return Response::download(public_path('/file/ProductImportSample.xlsx'));
        } catch (Exception $exception) {
            return response(['status' => false, 'message' => $exception->getMessage()], 422);
        }
    }

    public function import(ImportFileRequest $request)
    {
        try {
            Excel::import(new ProductImport($request->file('file')), $request->file('file'));
            return response('', 202);
        } catch (Exception $exception) {
            return response(['status' => false, 'message' => $exception->getMessage()], 422);
        }
    }

    public function downloadBarcode(Product $product)
    {
        try {
            return $this->productService->downloadBarcode($product);
        } catch (Exception $exception) {
            return response(['status' => false, 'message' => $exception->getMessage()], 422);
        }
    }

    public function barcodeProduct($barcode)
    {
        try {
            return response(['data' =>  $this->productService->barcodeProduct($barcode)]);
        } catch (Exception $exception) {
            return response(['status' => false, 'message' => $exception->getMessage()], 422);
        }
    }

    public function clearOffer(Product $product)
    {
        try {
            return new ProductAdminResource($this->productService->clearOffer($product));
        } catch (Exception $exception) {
            return response(['status' => false, 'message' => $exception->getMessage()], 422);
        }
    }
}
