<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ProductWithVariationResource extends JsonResource
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
            'product_id'        => $this->product_id,
            'item_id'           => $this->item_id,
            'variation_names'   => $this->variation_names,
            'product_name'      => $this?->product?->name,
            'sku'               => $this->sku,
            'price'             => $this->price,
            'quantity'          => abs($this->quantity),
            'discount'          => $this->discount,
            'tax'               => $this->tax,
            'taxes'             => $this->stockTaxes,
            'subtotal'          => $this->subtotal,
            'total'             => $this->total,
            'status'            => $this->status,
            'product_variation' => $this->variation_names ? true : false,
        ];
    }
}
