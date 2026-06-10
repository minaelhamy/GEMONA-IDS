<?php

namespace App\Http\Resources;

use App\Enums\Ask;
use App\Libraries\AppLibrary;
use App\Models\ProductTax;
use App\Models\ProductVariation;
use App\Models\Tax;
use Illuminate\Http\Resources\Json\JsonResource;
use Dipokhalder\Settings\Facades\Settings;

class OrderProductResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param \Illuminate\Http\Request $request
     * @return array
     */
    public function toArray($request): array
    {
        return [
            'id'                      => (int) $this->id,
            'order_id'                => (int) $this->model_id,
            'product_id'              => (int) $this->product_id,
            'product_name'            => $this->product?->name,
            'product_image'           => $this->product?->thumb,
            'product_slug'            => $this->product?->slug,
            'category_name'           => $this?->product?->category?->name,
            'price'                   => $this->price,
            'currency_price'          => AppLibrary::currencyAmountFormat($this->price),
            'quantity'                => abs($this->quantity),
            'order_quantity'          => abs($this->quantity),
            'discount'                => $this->discount,
            'discount_currency_price' => AppLibrary::currencyAmountFormat($this->discount),
            'tax'                     => $this->tax,
            'tax_currency'            => AppLibrary::currencyAmountFormat($this->tax),
            'subtotal'                => AppLibrary::flatAmountFormat($this->subtotal),
            'total'                   => AppLibrary::flatAmountFormat($this->total),
            'subtotal_currency_price' => AppLibrary::currencyAmountFormat($this->subtotal),
            'total_currency_price'    => AppLibrary::currencyAmountFormat($this->total),
            'status'                  => (int) $this->status,
            'variation_names'         => $this->variation_names,
            'product_user_review'     => $this?->product?->userReview ? true : false,
            'product_user_review_id'  => $this?->product?->userReview?->id,
            'is_refundable'           => $this?->product?->refundable === Ask::YES ? true : false,
            'has_variation'           => $this->item_type == ProductVariation::class ? true : false,
            'variation_id'            => $this->item_type == ProductVariation::class ? $this->item_id : '',
            'product_tax'             => $this->productTax($this->product_id),
        ];
    }

    public function productTax($productId)
    {

        return ProductTax::where('product_id', $productId)
            ->with('tax:id,name,tax_rate')
            ->get()
            ->map(function ($productTax) {
                return [
                    'tax_name' => $productTax->tax->name,
                    'tax_rate' => (float) $productTax->tax->tax_rate,
                ];
            })
            ->toArray();
    }
}
