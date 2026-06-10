<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class SimpleReviewResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            "star" => $this->star
        ];
    }
}
