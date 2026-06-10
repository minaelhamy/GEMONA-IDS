<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use App\Libraries\AppLibrary;

class PurchaseResource extends JsonResource
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
            'supplier_id'          => $this->supplier_id,
            'date'                 => $this->date,
            'converted_date'       => AppLibrary::datetime($this->date),
            'reference_no'         => $this->reference_no,
            'status'               => $this->status,
            'payment_status'       => $this->payment_status,
            'total'                => $this->total,
            'total_currency_price' => AppLibrary::currencyAmountFormat($this->total),
            'total_flat_price'     => AppLibrary::flatAmountFormat($this->total),
            'note'                 => $this->note,
            'supplier'             => $this->supplier
        ];
    }
}
