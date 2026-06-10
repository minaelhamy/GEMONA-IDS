<?php

namespace App\Http\Resources;


use Illuminate\Http\Resources\Json\JsonResource;

class SocialLoginSetupResource extends JsonResource
{

    public function toArray($request) : array
    {

         return [
             'id'=> $this->id,
             'name'=> $this->name,
             'slug'=> $this->slug,
             'status'=> $this->status,
             'options'=> GatewayOptionsResource::collection($this->gatewayOptions)
         ];
    }
}
