<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use App\Libraries\AppLibrary;
use Illuminate\Http\Resources\Json\JsonResource;

class simpleProductWithVariationCountResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id'                => $this->id,
            'name'              => $this->name,
            'buying_price'      => AppLibrary::convertAmountFormat($this->buying_price),
            'is_variation'      => count($this->variations) > 0 ? true : false,
            'sku'               => $this->sku,
            'taxes'             => $this->productTaxes->pluck('tax_id')
        ];
    }
}
