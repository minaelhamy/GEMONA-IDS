<?php

namespace App\Http\Resources;


use Illuminate\Http\Resources\Json\JsonResource;

class ShippingSetupResource extends JsonResource
{

    public $info;

    public function __construct($info)
    {
        parent::__construct($info);
        $this->info = $info;
    }

    /**
     * Transform the resource into an array.
     *
     * @param \Illuminate\Http\Request $request
     * @return array
     */
    public function toArray($request): array
    {
        return [
            'shipping_setup_method'                 => $this->info['shipping_setup_method'],
            'shipping_setup_flat_rate_wise_cost'    => $this->info['shipping_setup_flat_rate_wise_cost'],
            'shipping_setup_area_wise_default_cost' => $this->info['shipping_setup_area_wise_default_cost'],
        ];
    }
}
