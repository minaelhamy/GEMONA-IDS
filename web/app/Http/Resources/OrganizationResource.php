<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class OrganizationResource extends JsonResource
{
    public function toArray($request): array
    {
        return [
            'id'      => $this->id,
            'name'    => $this->name,
            'country' => $this->country,
            'city'    => $this->city,
            'address' => $this->address,
            'type'    => $this->type,
            'status'  => $this->status,
        ];
    }
}
