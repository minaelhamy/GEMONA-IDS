<?php

namespace App\Services;


use Exception;
use App\Models\Promotion;
use App\Models\PromotionProduct;
use Illuminate\Support\Facades\Log;
use App\Http\Requests\PaginateRequest;
use App\Http\Requests\PromotionProductRequest;
use App\Libraries\QueryExceptionLibrary;

class PromotionProductService
{
    protected $productExtraFilter = [
        'product_id',
        'name',
        'type',
        'status'
    ];

    /**
     * @throws Exception
     */
    public function list(PaginateRequest $request, Promotion $promotion)
    {
        try {
            $requests    = $request->all();
            $method      = $request->get('paginate', 0) == 1 ? 'paginate' : 'get';
            $methodValue = $request->get('paginate', 0) == 1 ? $request->get('per_page', 10) : '*';
            $orderColumn = $request->get('order_column') ?? 'id';
            $orderType   = $request->get('order_type') ?? 'desc';

            return PromotionProduct::with('promotion', 'product')->where(['promotion_id' => $promotion->id])->where(function ($query) use ($requests) {
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
    public function store(PromotionProductRequest $request, Promotion $promotion)
    {
        try {
            return PromotionProduct::create($request->validated() + ['promotion_id' => $promotion->id]);
        } catch (Exception $exception) {
            Log::info($exception->getMessage());
            throw new Exception(QueryExceptionLibrary::message($exception), 422);
        }
    }

    /**
     * @throws Exception
     */
    public function destroy(Promotion $promotion, PromotionProduct $promotionProduct)
    {
        try {
            if ($promotion->id == $promotionProduct->promotion_id) {
                $promotionProduct->delete();
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
    public function productWithPagination(PaginateRequest $paginateRequest, Promotion $promotion)
    {
        try {
            $perPage = $paginateRequest->get('per_page', 32);
            return $promotion->products()->select('products.id', 'products.name', 'products.sku', 'products.slug',  'products.selling_price', 'products.variation_price', 'products.add_to_flash_sale', 'products.offer_start_date', 'products.offer_end_date', 'products.discount', 'products.status')->withReviewRating()->with('media', 'variations', 'reviews')->active('products.status')->whereNull('deleted_at')->paginate($perPage);
        } catch (Exception $exception) {
            Log::info($exception->getMessage());
            throw new Exception(QueryExceptionLibrary::message($exception), 422);
        }
    }
}
