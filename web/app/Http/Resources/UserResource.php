<?php

namespace App\Http\Resources;


use App\Libraries\AppLibrary;
use Illuminate\Http\Resources\Json\JsonResource;

class UserResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param \Illuminate\Http\Request $request
     * @return array
     */
    public function toArray($request): array
    {
        $role = $this->effectiveRole;

        return [
            "id"               => $this->id,
            "name"             => $this->name,
            "phone"            => $this->phone === null ? '' : $this->phone,
            "email"            => $this->email,
            'username'         => $this->username,
            "balance"          => AppLibrary::flatAmountFormat($this->balance),
            "currency_balance" => AppLibrary::currencyAmountFormat($this->balance),
            "image"            => $this->thumb,
            "role_id"          => $this->myRole,
            "role_name"        => $role?->name,
            "country_code"     => $this->country_code,
            "country"          => $this->signup_country,
            "address"          => $this->signup_address,
            "organization"     => $this->organization ? [
                'id'      => $this->organization->id,
                'name'    => $this->organization->name,
                'country' => $this->organization->country,
                'type'    => $this->organization->type,
            ] : null,
            "order"            => $this->orders->count(),
            'create_date'      => AppLibrary::date($this->created_at),
            'update_date'      => AppLibrary::date($this->updated_at),
        ];
    }
}
