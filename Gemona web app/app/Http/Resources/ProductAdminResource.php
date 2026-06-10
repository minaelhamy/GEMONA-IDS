<?php

namespace App\Http\Resources;

use App\Enums\Ask;
use Carbon\Carbon;
use App\Libraries\AppLibrary;
use Illuminate\Http\Resources\Json\JsonResource;

class ProductAdminResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param \Illuminate\Http\Request $request
     * @return array
     */
    public function toArray($request): array
    {
        $price = count($this->variations) > 0 ? $this->variation_price : $this->selling_price;
        return [
            "id"                         => $this->id,
            "name"                       => $this->name,
            "sku"                        => $this->sku,
            "slug"                       => $this->slug,
            "product_category_id"        => $this->product_category_id,
            "barcode_id"                 => $this->barcode_id,
            "product_brand_id"           => $this->product_brand_id,
            "unit_id"                    => $this->unit_id,
            "tax_id"                     => ProductTaxResource::collection($this->taxes),
            "flat_buying_price"          => AppLibrary::flatAmountFormat($this->buying_price),
            "flat_selling_price"         => AppLibrary::flatAmountFormat($this->selling_price),
            "status"                     => $this->status,
            "can_purchasable"            => $this->can_purchasable,
            "show_stock_out"             => $this->show_stock_out,
            "maximum_purchase_quantity"  => $this->maximum_purchase_quantity,
            "low_stock_quantity_warning" => $this->low_stock_quantity_warning,
            "weight"                     => $this->weight,
            "warranty"                   => $this->warranty,
            "refundable"                 => $this->refundable,
            "description"                => $this->description === null ? '' : $this->description,
            "shipping_and_return"        => $this->shipping_and_return === null ? '' : $this->shipping_and_return,
            "product_tags"               => ProductTagResource::collection($this->tags),
            "category_name"              => $this?->category?->name,
            "order"                      => abs($this?->productOrders->sum('quantity')),
            'currency_price'             => AppLibrary::currencyAmountFormat($price),
            "cover"                      => $this->cover,
            'flash_sale'                 => $this->add_to_flash_sale == Ask::YES,
            'is_offer'                   => AppLibrary::isBetweenDate($this->offer_start_date, $this->offer_end_date),
            'discounted_price'           => AppLibrary::currencyAmountFormat($price - (($price / 100) * $this->discount)),
            'rating_star'                => $this->rating_star,
            'rating_star_count'          => $this->rating_star_count,
            "barcode_image"              => $this->barcodeImage,
        ];
    }
}