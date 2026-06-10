<?php

namespace App\Http\Resources;


use Illuminate\Http\Resources\Json\JsonResource;

class SupplierResource extends JsonResource
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
            "id"           => $this->id,
            "company"      => $this->company,
            "name"         => $this->name,
            "email"        => $this->email === null ? '' : $this->email,
            "phone"        => $this->phone === null ? '' : $this->phone,
            "country_code" => $this->country_code === null ? '' : $this->country_code,
            "address"      => $this->address === null ? '' : $this->address,
            "country"      => $this->country === null ? '' : $this->country,
            "state"        => $this->state === null ? '' : $this->state,
            "city"         => $this->city === null ? '' : $this->city,
            "zip_code"     => $this->zip_code === null ? '' : $this->zip_code,
            "image"        => $this->image,
        ];
    }
}