<?php

namespace App\Http\Resources;


use App\Libraries\AppLibrary;
use Illuminate\Http\Resources\Json\JsonResource;

class StockResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param \Illuminate\Http\Request $request
     * @return array
     */

    public function toArray($request)
    {
        return [

            'product_id'         => $this['product_id'],
            'product_name'       => $this['product_name'],
            'variation_names'    => $this['variation_names'],
            'status'             => $this['status'],
            'stock'              => $this['stock']

        ];
    }
}
