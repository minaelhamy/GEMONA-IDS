<?php

namespace App\Http\Resources;

use App\Libraries\AppLibrary;
use Illuminate\Http\Resources\Json\JsonResource;

class ProductReviewResource extends JsonResource
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
            "id"         => $this->id,
            "user_id"    => $this->user_id,
            "name"       => $this->user?->name,
            "product_id" => $this->product_id,
            "star"       => $this->star,
            "review"     => $this->review,
            "images"     => $this->images,
            "date"       => $this->created_at ? AppLibrary::date($this->created_at, 'd  M Y') : ''
        ];
    }
}
