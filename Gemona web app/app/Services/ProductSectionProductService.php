<?php

namespace App\Services;


use Exception;
use Illuminate\Support\Facades\Log;
use App\Models\ProductSectionProduct;
use App\Http\Requests\PaginateRequest;
use App\Http\Requests\ProductSectionProductRequest;
use App\Libraries\QueryExceptionLibrary;
use App\Models\ProductSection;

class ProductSectionProductService
{
    public $productExtra;
    protected $productExtraFilter = [
        'product_id',
        'name',
        'price',
        'status'
    ];

    /**
     * @throws Exception
     */
    public function list(PaginateRequest $request, ProductSection $productSection)
    {
        try {
            $requests    = $request->all();
            $method      = $request->get('paginate', 0) == 1 ? 'paginate' : 'get';
            $methodValue = $request->get('paginate', 0) == 1 ? $request->get('per_page', 10) : '*';
            $orderColumn = $request->get('order_column') ?? 'id';
            $orderType   = $request->get('order_type') ?? 'desc';

            return ProductSectionProduct::with('productSection', 'product')->where(['product_section_id' => $productSection->id])->where(function ($query) use ($requests) {
                foreach ($requests as $key => $request) {
                    if (in_array($key, $this->productExtraFilter)) {
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
    public function store(ProductSectionProductRequest $request, ProductSection $productSection)
    {
        try {
            return ProductSectionProduct::create($request->validated() + ['product_section_id' => $productSection->id]);
        } catch (Exception $exception) {
            Log::info($exception->getMessage());
            throw new Exception(QueryExceptionLibrary::message($exception), 422);
        }
    }

    /**
     * @throws Exception
     */
    public function destroy(ProductSection $productSection, ProductSectionProduct $productSectionProduct): void
    {
        try {
            if ($productSection->id == $productSectionProduct->product_section_id) {
                $productSectionProduct->delete();
            } else {
                throw new Exception(trans('all.product_match'), 422);
            }
        } catch (Exception $exception) {
            Log::info($exception->getMessage());
            throw new Exception(QueryExceptionLibrary::message($exception), 422);
        }
    }

    /**
     * @throws Exception
     */
    public function productWithPagination(PaginateRequest $paginateRequest, ProductSection $productSection)
    {
        try {
            $perPage = $paginateRequest->get('per_page', 32);
            return $productSection->products()
                ->select('products.id', 'products.name', 'products.sku', 'products.slug', 'products.selling_price', 'products.variation_price', 'products.add_to_flash_sale', 'products.offer_start_date', 'products.offer_end_date', 'products.discount', 'products.status')
                ->withReviewRating()
                ->with('media', 'variations', 'reviews')
                ->active('products.status')
                ->paginate($perPage);
        } catch (Exception $exception) {
            Log::info($exception->getMessage());
            throw new Exception(QueryExceptionLibrary::message($exception), 422);
        }
    }
}
