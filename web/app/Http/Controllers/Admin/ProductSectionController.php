<?php

namespace App\Http\Controllers\Admin;


use Exception;
use App\Models\ProductSection;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\ProductSectionExport;
use App\Http\Requests\PaginateRequest;
use App\Services\ProductSectionService;
use App\Http\Requests\ProductSectionRequest;
use App\Http\Resources\ProductSectionResource;
use Illuminate\Routing\Controllers\Middleware;
use Illuminate\Routing\Controllers\HasMiddleware;

class ProductSectionController extends AdminController implements HasMiddleware
{
    private ProductSectionService $productSectionService;

    public function __construct(ProductSectionService $productSectionService)
    {
        parent::__construct();
        $this->productSectionService = $productSectionService;
    }

    public static function middleware(): array
    {
        return [
            new Middleware('permission:product-sections', only: ['index']),
            new Middleware('permission:product-sections', only: ['export']),
            new Middleware('permission:product-sections_create', only: ['store']),
            new Middleware('permission:product-sections_edit', only: ['update']),
            new Middleware('permission:product-sections_delete', only: ['destroy']),
            new Middleware('permission:product-sections_show', only: ['show']),
        ];
    }

    public function index(PaginateRequest $request): \Illuminate\Http\Response|\Illuminate\Http\Resources\Json\AnonymousResourceCollection|\Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\Routing\ResponseFactory
    {
        try {
            return ProductSectionResource::collection($this->productSectionService->list($request));
        } catch (Exception $exception) {
            return response(['status' => false, 'message' => $exception->getMessage()], 422);
        }
    }

    public function store(ProductSectionRequest $request): \Illuminate\Http\Response|ProductSectionResource|\Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\Routing\ResponseFactory
    {
        try {
            return new ProductSectionResource($this->productSectionService->store($request));
        } catch (Exception $exception) {
            return response(['status' => false, 'message' => $exception->getMessage()], 422);
        }
    }

    public function show(ProductSection $productSection): \Illuminate\Http\Response|ProductSectionResource|\Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\Routing\ResponseFactory
    {
        try {
            return new ProductSectionResource($this->productSectionService->show($productSection));
        } catch (Exception $exception) {
            return response(['status' => false, 'message' => $exception->getMessage()], 422);
        }
    }

    public function update(ProductSectionRequest $request, ProductSection $productSection): \Illuminate\Http\Response|ProductSectionResource|\Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\Routing\ResponseFactory
    {
        try {
            return new ProductSectionResource($this->productSectionService->update($request, $productSection));
        } catch (Exception $exception) {
            return response(['status' => false, 'message' => $exception->getMessage()], 422);
        }
    }

    public function destroy(ProductSection $productSection): \Illuminate\Http\Response|\Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\Routing\ResponseFactory
    {
        try {
            $this->productSectionService->destroy($productSection);
            return response('', 202);
        } catch (Exception $exception) {
            return response(['status' => false, 'message' => $exception->getMessage()], 422);
        }
    }

    public function export(PaginateRequest $request): \Illuminate\Foundation\Application|\Illuminate\Http\Response|\Symfony\Component\HttpFoundation\BinaryFileResponse|\Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\Routing\ResponseFactory
    {
        try {
            return Excel::download(new ProductSectionExport($this->productSectionService, $request), 'Product_section.xlsx');
        } catch (Exception $exception) {
            return response(['status' => false, 'message' => $exception->getMessage()], 422);
        }
    }
}
