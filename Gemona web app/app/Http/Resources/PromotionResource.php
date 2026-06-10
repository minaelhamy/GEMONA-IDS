<?php

namespace App\Http\Resources;


use App\Libraries\AppLibrary;
use Illuminate\Http\Resources\Json\JsonResource;

class PromotionResource extends JsonResource
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
            'id'      => $this->id,
            'name'    => $this->name,
            "slug"    => $this->slug,
            "type"    => $this->type,
            'status'  => $this->status,
            'cover'   => $this->cover,
            'preview' => $this->preview
        ];
    }
}
