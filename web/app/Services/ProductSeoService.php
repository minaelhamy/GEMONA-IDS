<?php

namespace App\Services;

use Exception;
use App\Models\Product;
use App\Models\ProductSeo;
use Illuminate\Support\Facades\Log;
use App\Http\Requests\ProductSeoRequest;
use App\Libraries\QueryExceptionLibrary;

class ProductSeoService
{
    /**
     * @throws Exception
     */
    public function list(Product $product)
    {
        try {
            return ProductSeo::where('product_id', $product->id)->first();
        } catch (Exception $exception) {
            Log::info($exception->getMessage());
            throw new Exception(QueryExceptionLibrary::message($exception), 422);
        }
    }

    /**
     * @throws Exception
     */
    public function update(ProductSeoRequest $request, Product $product)
    {
        try {
            $productSeo = ProductSeo::where('product_id', $product->id)->first();
            if (!blank($productSeo)) {
                $productSeo->update($request->validated());
            } else {
                $productSeo = ProductSeo::create($request->validated());
            }
            if ($request->image) {
                $productSeo->clearMediaCollection('product-seo');
                $productSeo->addMediaFromRequest('image')->toMediaCollection('product-seo');
            }
            return $productSeo;
        } catch (Exception $exception) {
            Log::info($exception->getMessage());
            throw new Exception(QueryExceptionLibrary::message($exception), 422);
        }
    }
}
