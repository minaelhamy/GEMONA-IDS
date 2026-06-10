<?php

namespace App\Http\Resources;


use Illuminate\Http\Resources\Json\JsonResource;

class ProductVideoResource extends JsonResource
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
            'id'             => $this->id,
            'product_id'     => $this->product_id,
            'video_provider' => $this->video_provider,
            'provider_name'  => trans('videoProvider.' . $this->video_provider),
            'link'           => $this->link
        ];
    }
}
