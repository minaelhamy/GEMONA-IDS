<?php

namespace App\Http\Controllers\Admin;


use Exception;
use App\Models\ProductBrand;
use App\Services\ProductBrandService;
use App\Http\Requests\PaginateRequest;
use App\Http\Requests\ProductBrandRequest;
use App\Http\Resources\ProductBrandResource;
use Illuminate\Routing\Controllers\Middleware;
use Illuminate\Routing\Controllers\HasMiddleware;

class ProductBrandController extends AdminController implements HasMiddleware
{
    private ProductBrandService $productBrandService;

    public function __construct(ProductBrandService $productBrandService)
    {
        parent::__construct();
        $this->productBrandService = $productBrandService;
    }

    public static function middleware(): array
    {
        return [
            new Middleware('permission:settings|products|pos', only: ['index']),
            new Middleware('permission:settings', only: ['store', 'update', 'destroy', 'show']),
        ];
    }

    public function index(PaginateRequest $request): \Illuminate\Http\Response | \Illuminate\Http\Resources\Json\AnonymousResourceCollection | \Illuminate\Contracts\Foundation\Application | \Illuminate\Contracts\Routing\ResponseFactory {
        try {
            return ProductBrandResource::collection($this->productBrandService->list($request));
        } catch (Exception $exception) {
            return response(['status' => false, 'message' => $exception->getMessage()], 422);
        }
    }


    public function store(ProductBrandRequest $request): \Illuminate\Http\Response | ProductBrandResource | \Illuminate\Contracts\Foundation\Application | \Illuminate\Contracts\Routing\ResponseFactory {
        try {
            return new ProductBrandResource($this->productBrandService->store($request));
        } catch (Exception $exception) {
            return response(['status' => false, 'message' => $exception->getMessage()], 422);
        }
    }

    public function show(
        ProductBrand $productBrand
    ): \Illuminate\Http\Response | ProductBrandResource | \Illuminate\Contracts\Foundation\Application | \Illuminate\Contracts\Routing\ResponseFactory {
        try {
            return new ProductBrandResource($this->productBrandService->show($productBrand));
        } catch (Exception $exception) {
            return response(['status' => false, 'message' => $exception->getMessage()], 422);
        }
    }

    public function update(
        ProductBrandRequest $request,
        ProductBrand $productBrand
    ): \Illuminate\Http\Response | ProductBrandResource | \Illuminate\Contracts\Foundation\Application | \Illuminate\Contracts\Routing\ResponseFactory {
        try {
            if (env('DEMO')) {
                return response(['status' => false, 'message' => 'This action is not allowed in demo mode.'], 422);
            }  else {
            return new ProductBrandResource($this->productBrandService->update($request, $productBrand));
            }
        } catch (Exception $exception) {
            return response(['status' => false, 'message' => $exception->getMessage()], 422);
        }
    }

    public function destroy(
        ProductBrand $productBrand
    ): \Illuminate\Http\Response | \Illuminate\Contracts\Foundation\Application | \Illuminate\Contracts\Routing\ResponseFactory {
        try {
            if (env('DEMO')) {
                return response(['status' => false, 'message' => 'This action is not allowed in demo mode.'], 422);
            }  else {
            $this->productBrandService->destroy($productBrand);
            }
            return response('', 202);
        } catch (Exception $exception) {
            return response(['status' => false, 'message' => $exception->getMessage()], 422);
        }
    }
}
