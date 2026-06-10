<?php

namespace App\Http\Resources;

use App\Enums\Ask;
use Carbon\Carbon;
use App\Libraries\AppLibrary;
use Illuminate\Http\Resources\Json\JsonResource;

class ProductSectionProductResource extends JsonResource
{

    /**
     * Transform the resource into an array.
     *
     * @param \Illuminate\Http\Request $request
     * @return array
     */

    public function toArray($request): array
    {
        $price = count($this->product?->variations) > 0 ? $this->product?->variation_price : $this->product?->selling_price;
        return [
            'id'                                        => $this->id,
            'productSection_id'                         => $this->product_section_id,
            'productSection_product_id'                 => $this->product_id,
            'productSection_name'                       => optional($this->productSection)->name,
            'productSection_product_name'               => optional($this->product)->name,
            "productSection_product_flat_selling_price" => AppLibrary::flatAmountFormat($this->product?->selling_price),
            'productSection_product_status'             => optional($this->product)->status,
            'currency_price'                            => AppLibrary::currencyAmountFormat($price),
            'flash_sale'                                => $this->product?->add_to_flash_sale == Ask::YES,
            'is_offer'                                  => AppLibrary::isBetweenDate($this->product?->offer_start_date, $this->product?->offer_end_date),
            'discounted_price'                          => AppLibrary::currencyAmountFormat($price - (($price / 100) * $this->product?->discount))
        ];
    }
}