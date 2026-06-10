<?php

namespace App\Http\Resources;


use Illuminate\Http\Resources\Json\JsonResource;

class ProductSeoResource extends JsonResource
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
            'id'           => $this->id ?? '',
            'product_id'   => $this->product_id ?? '',
            'title'        => $this->title ?? '',
            'description'  => $this->description ?? '',
            'meta_keyword' => $this->meta_keyword ?? [],
            'thumb'        => $this->thumb ?? '',
            'cover'        => $this->cover ?? ''
        ];
    }
}
