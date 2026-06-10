<?php

namespace App\Http\Resources;


use App\Libraries\AppLibrary;
use Illuminate\Http\Resources\Json\JsonResource;

class ProductVariationResource extends JsonResource
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
            'id'                            => $this->id,
            'product_id'                    => $this->product_id,
            'product_attribute_id'          => $this->product_attribute_id,
            'product_attribute_option_id'   => $this->product_attribute_option_id,
            'price'                         => AppLibrary::flatAmountFormat($this->price),
            'sku'                           => $this->sku,
            'parent_id'                     => $this->parent_id,
            'order'                         => $this->order,
            'product'                       => $this->product->name,
            'product_attribute_name'        => $this->productAttribute->name,
            'product_attribute_option_name' => $this->productAttributeOption->name,
            'product_attribute'             => $this->productAttribute,
            'product_attribute_option'      => $this->productAttributeOption
        ];
    }
}
