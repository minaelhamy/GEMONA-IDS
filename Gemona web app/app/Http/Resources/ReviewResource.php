<?php

namespace App\Http\Resources;


use Illuminate\Http\Resources\Json\JsonResource;

class ReviewResource extends JsonResource
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
            "id"            => $this->id,
            "user_id"       => $this->user_id,
            "user_name"     => $this->user?->name,
            "product_id"    => $this->product_id,
            "product_name"  => $this->product?->name,
            "star"          => $this->star,
            "review"        => $this->review,
            "images"        => $this->images,
        ]; 
    }
}
