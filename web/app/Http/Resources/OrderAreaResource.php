<?php

namespace App\Http\Resources;


use Illuminate\Http\Resources\Json\JsonResource;

class OrderAreaResource extends JsonResource
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
            'id'            => $this->id,
            'country'       => $this->country,
            'state'         => $this->state,
            'city'          => $this->city,
            'shipping_cost' => $this->shipping_cost,
            'status'        => $this->status,
        ];
    }
}
