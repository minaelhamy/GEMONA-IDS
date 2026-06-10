<?php

namespace App\Http\Resources;

use App\Enums\TaxType;
use App\Models\Currency;
use App\Libraries\AppLibrary;
use Illuminate\Http\Resources\Json\JsonResource;

class ReturnAndRefundProductResource extends JsonResource
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
            'id'                    => $this->id,
            'product_id'            => $this->product_id,
            'product_name'          => $this?->product?->name,
            'product_image'         => $this?->product?->thumb,
            'variation_names'       => $this->variation_names,
            'category_name'         => $this?->product?->category?->name,
            'quantity'              => $this->quantity,
            'price'                 => $this->price,
            'currency_price'        => AppLibrary::currencyAmountFormat($this->price),
            'total_currency_price'  => AppLibrary::currencyAmountFormat($this->total),
            'return_currency_price' => AppLibrary::currencyAmountFormat($this->return_price),
        ];
    }
}