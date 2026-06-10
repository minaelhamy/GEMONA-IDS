<?php

namespace App\Http\Controllers\Admin;


use Exception;
use App\Models\Product;
use App\Models\ProductVideo;
use App\Services\ProductVideoService;
use App\Http\Requests\PaginateRequest;
use App\Http\Requests\ProductVideoRequest;
use App\Http\Resources\ProductVideoResource;
use Illuminate\Routing\Controllers\Middleware;
use App\Http\Controllers\Admin\AdminController;
use Illuminate\Routing\Controllers\HasMiddleware;

class ProductVideoController extends AdminController implements HasMiddleware
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */

    private ProductVideoService $productVideoService;

    public function __construct(ProductVideoService $productVideoService)
    {
        parent::__construct();
        $this->productVideoService = $productVideoService;
    }

    public static function middleware(): array
    {
        return [
            new Middleware('permission:products_show', only: ['index', 'store', 'update', 'destroy']),
        ];
    }

    public function index(PaginateRequest $request, Product $product)
    {
        try {
            return ProductVideoResource::collection($this->productVideoService->list($request, $product));
        } catch (Exception $exception) {
            return response(['status' => false, 'message' => $exception->getMessage()], 422);
        }
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(ProductVideoRequest $request, Product $product): ProductVideoResource | \Illuminate\Http\Response
    {
        try {
            return new ProductVideoResource($this->productVideoService->store($request, $product));
        } catch (Exception $exception) {
            return response(['status' => false, 'message' => $exception->getMessage()], 422);
        }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(ProductVideoRequest $request, Product $product, ProductVideo $productVideo): ProductVideoResource | \Illuminate\Http\Response
    {
        try {
            return new ProductVideoResource($this->productVideoService->update($request, $product, $productVideo));
        } catch (Exception $exception) {
            return response(['status' => false, 'message' => $exception->getMessage()], 422);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Product $product, ProductVideo $productVideo)
    {
        try {
            $this->productVideoService->destroy($product, $productVideo);
            return response('', 202);
        } catch (Exception $exception) {
            return response(['status' => false, 'message' => $exception->getMessage()], 422);
        }
    }
}
