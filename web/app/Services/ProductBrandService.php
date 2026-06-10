<?php

namespace App\Services;


use Exception;
use Illuminate\Support\Str;
use App\Models\ProductBrand;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Http\Requests\PaginateRequest;
use App\Libraries\QueryExceptionLibrary;
use App\Http\Requests\ProductBrandRequest;

class ProductBrandService
{
    protected $productCateFilter = [
        'name',
        'slug',
        'description',
        'status',
    ];

    protected $exceptFilter = [
        'excepts'
    ];

    /**
     * @throws Exception
     */
    public function list(PaginateRequest $request)
    {
        try {
            $requests    = $request->all();
            $method      = $request->get('paginate', 0) == 1 ? 'paginate' : 'get';
            $methodValue = $request->get('paginate', 0) == 1 ? $request->get('per_page', 10) : '*';
            $orderColumn = $request->get('order_column') ?? 'id';
            $orderType   = $request->get('order_type') ?? 'desc';

            return ProductBrand::where(function ($query) use ($requests) {
                foreach ($requests as $key => $request) {
                    if (in_array($key, $this->productCateFilter)) {
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
    public function store(ProductBrandRequest $request)
    {
        try {
            $productBrand = ProductBrand::create($request->validated() + ['slug' => Str::slug($request->name)]);
            if ($request->image) {
                $productBrand->addMediaFromRequest('image')->toMediaCollection('product-brand');
            }
            return $productBrand;
        } catch (Exception $exception) {
            Log::info($exception->getMessage());
            throw new Exception(QueryExceptionLibrary::message($exception), 422);
        }
    }

    /**
     * @throws Exception
     */
    public function update(ProductBrandRequest $request, ProductBrand $productBrand): ProductBrand
    {
        try {
            $productBrand->update($request->validated() + ['slug' => Str::slug($request->name)]);
            if ($request->image) {
                $productBrand->clearMediaCollection('product-brand');
                $productBrand->addMediaFromRequest('image')->toMediaCollection('product-brand');
            }
            return $productBrand;
        } catch (Exception $exception) {
            Log::info($exception->getMessage());
            throw new Exception(QueryExceptionLibrary::message($exception), 422);
        }
    }

    /**
     * @throws Exception
     */
    public function destroy(ProductBrand $productBrand)
    {
        try {
            $checkProduct = $productBrand->products->whereNull('deleted_at');
            if (!blank($checkProduct)) {
                $productBrand->delete();
            } else {
                DB::statement('SET FOREIGN_KEY_CHECKS=0');
                $productBrand->delete();
                DB::statement('SET FOREIGN_KEY_CHECKS=1');
            }
        } catch (Exception $exception) {
            Log::info($exception->getMessage());
            throw new Exception(QueryExceptionLibrary::message($exception), 422);
        }
    }

    /**
     * @throws Exception
     */
    public function show(ProductBrand $productBrand)
    {
        try {
            return $productBrand;
        } catch (Exception $exception) {
            Log::info($exception->getMessage());
            throw new Exception(QueryExceptionLibrary::message($exception), 422);
        }
    }
}
