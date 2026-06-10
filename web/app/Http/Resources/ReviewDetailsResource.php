<?php

namespace App\Http\Resources;

use App\Libraries\AppLibrary;
use Illuminate\Http\Resources\Json\JsonResource;

class ReviewDetailsResource extends JsonResource
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
            "id"              => $this->id,
            "user_name"       => $this->user?->name,
            "product_name"    => $this->product?->name,
            "product_brand"   => $this->product?->brand?->name,
            "product_sku"     => $this->product?->sku,
            "buying_price"    => AppLibrary::flatAmountFormat($this->product?->buying_price),
            "selling_price"   => AppLibrary::flatAmountFormat($this->product?->selling_price),
            "warranty"        => $this->product?->warranty,
            "weight"          => $this->product?->weight,
            "unit_name"       => $this->product?->unit?->name,
            "star"            => $this->star,
            "review"          => $this->review,
            "images"          => $this->images,
        ];
    }
}
