<?php

namespace App\Http\Resources;


use App\Enums\Status;
use App\Libraries\AppLibrary;
use Illuminate\Http\Resources\Json\JsonResource;

class SimpleTaxResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param \Illuminate\Http\Request $request
     * @return array
     */
    public function toArray($request): array
    {
        $tax = $this->tax;
        if($tax->status == Status::ACTIVE) {
            return [
                "id"       => $this->id,
                "name"     => $tax?->name,
                "code"     => $tax?->code,
                "tax_rate" => AppLibrary::flatAmountFormat($tax?->tax_rate),
                "status"   => $tax->status,
            ];
        } else {
            return [];
        }
    }
}
