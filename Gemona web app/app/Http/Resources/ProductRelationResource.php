<?php

namespace App\Http\Resources;


use App\Libraries\AppLibrary;
use Illuminate\Http\Resources\Json\JsonResource;

class ProductRelationResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param \Illuminate\Http\Request $request
     * @return array
     */
    public function toArray($request)
    {

        $products = $this['products']->toArray();
        return [
            'products'       => SimpleProductResource::collection($this['products']),
            'brands'         => SimpleProductBrandResource::collection($this['brands']),
            'variations'     => (object)$this['variations'],
            'max_price'      => AppLibrary::convertAmountFormat($this['max_price']),
            'current_page'   => $products['current_page'],
            'first_page_url' => $products['first_page_url'],
            'from'           => $products['from'],
            'last_page'      => $products['last_page'],
            'last_page_url'  => $products['last_page_url'],
            'links'          => $products['links'],
            'next_page_url'  => $products['next_page_url'],
            'path'           => $products['path'],
            'per_page'       => $products['per_page'],
            'prev_page_url'  => $products['prev_page_url'],
            'to'             => $products['to'],
            'total'          => $products['total'],
        ];
    }
}
