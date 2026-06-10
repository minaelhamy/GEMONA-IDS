<?php

namespace App\Http\Resources;

use App\Enums\Ask;
use App\Enums\Activity;
use App\Libraries\AppLibrary;
use Illuminate\Http\Resources\Json\JsonResource;

class ProductDetailsAdminResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param \Illuminate\Http\Request $request
     * @return array
     */
    public function toArray($request): array
    {
        $price = count($this?->variations) > 0 ? $this->variation_price : $this->selling_price;
        return [
            "id"                           => $this->id,
            "name"                         => $this->name,
            "sku"                          => $this->sku,
            "category"                     => $this->category?->name,
            "brand"                        => $this->brand?->name,
            "barcode"                      => $this->barcode?->name,
            "tax"                          => AppLibrary::taxString($this?->taxes),
            "flat_buying_price"            => AppLibrary::flatAmountFormat($this->buying_price),
            "flat_selling_price"           => AppLibrary::flatAmountFormat($this->selling_price),
            "maximum_purchase_quantity"    => $this->maximum_purchase_quantity,
            "low_stock_quantity_warning"   => $this->low_stock_quantity_warning,
            "weight"                       => $this->weight,
            "warranty"                     => $this->warranty,
            "unit"                         => $this->unit?->name,
            "can_purchasable"              => $this->can_purchasable,
            "show_stock_out"               => $this->show_stock_out,
            "refundable"                   => $this->refundable,
            "status"                       => $this->status,
            "tags"                         => AppLibrary::tagString($this?->tags),
            "description"                  => $this->description === null ? '' : $this->description,
            "preview"                      => $this->preview,
            "image"                        => $this->preview,
            "images"                       => $this->previews,
            "shipping_and_return"          => $this->shipping_and_return === null ? '' : $this->shipping_and_return,
            "add_to_flash_sale"            => $this->add_to_flash_sale,
            "offer_start_date"             => $this->offer_start_date,
            "offer_end_date"               => $this->offer_end_date,
            "shipping_type"                => $this->shipping_type,
            "shipping_cost"                => AppLibrary::flatAmountFormat($this->shipping_cost),
            "is_product_quantity_multiply" => $this->is_product_quantity_multiply,
            'category_slug'                => $this->category?->slug,
            'price'                        => AppLibrary::isBetweenDate($this->offer_start_date, $this->offer_end_date) ? AppLibrary::convertAmountFormat($price - (($price / 100) * $this->discount)) : AppLibrary::convertAmountFormat($price),
            'currency_price'               => AppLibrary::currencyAmountFormat(AppLibrary::isBetweenDate($this->offer_start_date, $this->offer_end_date) ? AppLibrary::convertAmountFormat($price - (($price / 100) * $this->discount)) : AppLibrary::convertAmountFormat($price)),
            'old_price'                    => AppLibrary::convertAmountFormat($price),
            'old_currency_price'           => AppLibrary::currencyAmountFormat($price),
            'discount'                     => AppLibrary::isBetweenDate($this->offer_start_date, $this->offer_end_date) ? AppLibrary::convertAmountFormat(($price / 100) * $this->discount) : 0,
            'discount_percentage'          => AppLibrary::convertAmountFormat($this->discount),
            'flash_sale'                   => $this->add_to_flash_sale == Ask::YES,
            'is_offer'                     => AppLibrary::isBetweenDate($this->offer_start_date, $this->offer_end_date),
            'rating_star'                  => $this->rating_star,
            'rating_star_count'            => $this->rating_star_count,
            'stock'                        => $this->show_stock_out == Activity::DISABLE ? $this->can_purchasable == Ask::NO ? (int)env('NON_PURCHASE_QUANTITY') : (int)$this->stock_items_sum_quantity : 0,
            'taxes'                        => SimpleTaxResource::collection($this->taxes),
            'thumb'                        => $this->thumb,
            "barcode_image"                => $this->barcodeImage,
        ];
    }
}