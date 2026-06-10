<?php

namespace App\Http\Resources;

use App\Enums\Activity;
use App\Enums\Ask;
use App\Libraries\AppLibrary;
use Illuminate\Http\Resources\Json\JsonResource;

class SimpleProductDetailsResource extends JsonResource
{

    public function toArray($request): array
    {
        $price = count($this->variations) > 0 ? $this->variation_price : $this->selling_price;

        return [
            'id'                        => $this->id,
            'name'                      => $this->name,
            'slug'                      => $this->slug,
            'price'                     => AppLibrary::isBetweenDate($this->offer_start_date, $this->offer_end_date) ? AppLibrary::convertAmountFormat($price - (($price / 100) * $this->discount)) : AppLibrary::convertAmountFormat($price),
            'currency_price'            => AppLibrary::currencyAmountFormat(AppLibrary::isBetweenDate($this->offer_start_date, $this->offer_end_date) ? AppLibrary::convertAmountFormat($price - (($price / 100) * $this->discount)) : AppLibrary::convertAmountFormat($price)),
            'old_price'                 => AppLibrary::convertAmountFormat($price),
            'old_currency_price'        => AppLibrary::currencyAmountFormat($price),
            'discount'                  => AppLibrary::isBetweenDate($this->offer_start_date, $this->offer_end_date) ? AppLibrary::convertAmountFormat(($price / 100) * $this->discount) : 0,
            'discount_percentage'       => AppLibrary::convertAmountFormat($this->discount),
            'flash_sale'                => $this->add_to_flash_sale == Ask::YES,
            'is_offer'                  => AppLibrary::isBetweenDate($this->offer_start_date, $this->offer_end_date),
            'rating_star'               => $this->rating_star,
            'rating_star_count'         => (int) $this->rating_star_count,
            'image'                     => $this->cover,
            'images'                    => $this->previews,
            'taxes'                     => SimpleTaxResource::collection($this->taxes),
            'reviews'                   => ProductReviewResource::collection($this->reviews),
            'videos'                    => ProductVideoResource::collection($this->videos),
            'seo'                       => new ProductSeoResource($this->seo),
            'wishlist'                  => (bool)$this->wishlist,
            'details'                   => $this->description,
            'shipping_and_return'       => $this->shipping_and_return,
            'category_slug'             => $this->category?->slug,
            'unit'                      => $this->unit?->name,
            'stock'                     => $this->show_stock_out == Activity::DISABLE ? ($this->can_purchasable == Ask::NO ? (int)env('NON_PURCHASE_QUANTITY') : (int)$this->stock_items_sum_quantity) : 0,
            'sku'                       => $this->sku,
            "maximum_purchase_quantity" => $this->maximum_purchase_quantity,
            'shipping'                  => [
                'shipping_type'                => $this->shipping_type,
                'shipping_cost'                => $this->shipping_cost,
                'is_product_quantity_multiply' => $this->is_product_quantity_multiply
            ]
        ];
    }
}
