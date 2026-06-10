<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use App\Libraries\AppLibrary;


class DamageResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id'                   => $this->id,
            'date'                 => $this->date,
            'converted_date'       => AppLibrary::datetime($this->date),
            'reference_no'         => $this->reference_no,
            'total'                => $this->total,
            'total_currency_price' => AppLibrary::currencyAmountFormat($this->total),
            'total_flat_price'     => AppLibrary::flatAmountFormat($this->total),
            'note'                 => $this->note,
        ];
    }
}
