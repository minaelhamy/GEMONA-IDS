<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class OutletResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            "id"           => $this->id,
            "name"         => $this->name,
            "email"        => $this->email === null ? '' : $this->email,
            "phone"        => $this->phone === null ? '' : $this->phone,
            "country_code" => $this->country_code === null ? '' : $this->country_code,
            "latitude"     => $this->latitude === null ? '' : $this->latitude,
            "longitude"    => $this->longitude === null ? '' : $this->longitude,
            "city"         => $this->city,
            "state"        => $this->state,
            "zip_code"     => $this->zip_code,
            "address"      => $this->address,
            "status"       => $this->status
        ];
    }
}
