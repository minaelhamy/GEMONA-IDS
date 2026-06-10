<?php

namespace App\Services;


use Exception;
use Illuminate\Support\Facades\Log;
use App\Http\Requests\PaginateRequest;
use App\Http\Requests\ProductVideoRequest;
use App\Libraries\QueryExceptionLibrary;
use App\Models\Product;
use App\Models\ProductVideo;
use Illuminate\Support\Facades\DB;

class ProductVideoService
{
    public object $productVideo;
    protected $videoFilter = [
        'video_provider',
    ];

    protected $exceptFilter = [
        'excepts'
    ];

    /**
     * @throws Exception
     */
    public function list(PaginateRequest $request, Product $product)
    {
        try {
            $requests    = $request->all();
            $method      = $request->get('paginate', 0) == 1 ? 'paginate' : 'get';
            $methodValue = $request->get('paginate', 0) == 1 ? $request->get('per_page', 10) : '*';
            $orderColumn = $request->get('order_column') ?? 'id';
            $orderType   = $request->get('order_type') ?? 'desc';

            return ProductVideo::where(['product_id' => $product->id])->where(function ($query) use ($requests) {
                foreach ($requests as $key => $request) {
                    if (in_array($key, $this->videoFilter)) {
                        $query->where($key, 'like', '%' . $request . '%');
                    }

                    if (in_array($key, $this->exceptFilter)) {
                        $explodes = explode('|', $request);
                        if (is_array($explodes)) {
                            foreach ($explodes as $explode) {
                                $query->where('id', '!=', $explode);
                            }
                        }
                    }
                }
            })->orderBy($orderColumn, $orderType)->$method(
                $methodValue
            );
        } catch (Exception $exception) {
            Log::info($exception->getMessage());
            throw new Exception(QueryExceptionLibrary::message($exception), 422);
        }
    }

    /**
     * @throws Exception
     */
    public function store(ProductVideoRequest $request, Product $product)
    {
        try {
            DB::transaction(function () use ($request, $product) {
                $this->productVideo = ProductVideo::create($request->validated() + ['product_id' => $product->id]);
            });
            return $this->productVideo;
        } catch (Exception $exception) {
            Log::info($exception->getMessage());
            DB::rollBack();
            throw new Exception(QueryExceptionLibrary::message($exception), 422);
        }
    }

    /**
     * @throws Exception
     */
    public function update(ProductVideoRequest $request, Product $product, ProductVideo $productVideo)
    {
        try {
            DB::transaction(function () use ($request, $product, $productVideo) {
                if ($productVideo->product_id == $product->id) {
                    $productVideo->update($request->validated());
                }
            });
            return $productVideo;
        } catch (Exception $exception) {
            Log::info($exception->getMessage());
            DB::rollBack();
            throw new Exception(QueryExceptionLibrary::message($exception), 422);
        }
    }

    /**
     * @throws Exception
     */
    public function destroy(Product $product, ProductVideo $productVideo)
    {
        try {
            DB::transaction(function () use ($product, $productVideo) {
                if ($productVideo->product_id == $product->id) {
                    $productVideo->delete();
                }
            });
        } catch (Exception $exception) {
            Log::info($exception->getMessage());
            DB::rollBack();
            throw new Exception(QueryExceptionLibrary::message($exception), 422);
        }
    }
}
