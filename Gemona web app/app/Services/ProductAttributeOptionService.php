<?php

namespace App\Services;


use App\Http\Requests\ProductAttributeOptionRequest;
use App\Http\Requests\PaginateRequest;
use App\Libraries\QueryExceptionLibrary;
use App\Models\ProductAttribute;
use App\Models\ProductAttributeOption;
use Exception;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class ProductAttributeOptionService
{
    public object $productAttributeOption;
    protected array $productAttributeOptionFilter = [
        'name'
    ];

    /**
     * @throws Exception
     */
    public function list(PaginateRequest $request, ProductAttribute $productAttribute)
    {
        try {
            $requests = $request->all();
            $method = $request->get('paginate', 0) == 1 ? 'paginate' : 'get';
            $methodValue = $request->get('paginate', 0) == 1 ? $request->get('per_page', 10) : '*';
            $orderColumn = $request->get('order_column') ?? 'id';
            $orderType = $request->get('order_type') ?? 'desc';

            return ProductAttributeOption::where(['product_attribute_id' => $productAttribute->id])->where(function ($query) use ($requests) {
                foreach ($requests as $key => $request) {
                    if (in_array($key, $this->productAttributeOptionFilter)) {
                        $query->where($key, 'like', '%' . $request . '%');
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
    public function store(ProductAttributeOptionRequest $request, ProductAttribute $productAttribute): object
    {
        try {
            DB::transaction(function () use ($request, $productAttribute) {
                $this->productAttributeOption = ProductAttributeOption::create($request->validated() + ['product_attribute_id' => $productAttribute->id]);
            });
            return $this->productAttributeOption;
        } catch (Exception $exception) {
            Log::info($exception->getMessage());
            DB::rollBack();
            throw new Exception(QueryExceptionLibrary::message($exception), 422);
        }
    }

    /**
     * @throws Exception
     */
    public function update(ProductAttributeOptionRequest $request, ProductAttribute $productAttribute, ProductAttributeOption $productAttributeOption): ProductAttributeoption
    {
        try {
            DB::transaction(function () use ($request, $productAttribute, $productAttributeOption) {
                if ($productAttributeOption->product_attribute_id == $productAttribute->id) {
                    $productAttributeOption->update($request->validated());
                }
            });
            return $productAttributeOption;
        } catch (Exception $exception) {
            Log::info($exception->getMessage());
            DB::rollBack();
            throw new Exception(QueryExceptionLibrary::message($exception), 422);
        }
    }

    /**
     * @throws Exception
     */
    public function destroy(ProductAttribute $productAttribute, ProductAttributeOption $productAttributeOption): void
    {
        try {
            DB::transaction(function () use ($productAttribute, $productAttributeOption) {
                if ($productAttributeOption->product_attribute_id == $productAttribute->id) {
                    $productAttributeOption->delete();
                }
            });
        } catch (Exception $exception) {
            Log::info($exception->getMessage());
            DB::rollBack();
            throw new Exception(QueryExceptionLibrary::message($exception), 422);
        }
    }

    /**
     * @throws Exception
     */
    public function show(ProductAttribute $productAttribute, ProductAttributeOption $productAttributeOption)
    {
        try {
            if ($productAttributeOption->product_attribute_id == $productAttribute->id) {
                return $productAttributeOption;
            }
        } catch (Exception $exception) {
            Log::info($exception->getMessage());
            throw new Exception(QueryExceptionLibrary::message($exception), 422);
        }
    }
}
