<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use App\Libraries\AppLibrary;

class PurchasePaymentResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id'             => $this->id,
            'purchase_id'    => $this->purchase_id,
            'date'           => $this->date,
            'converted_date' => AppLibrary::datetime($this->date),
            'reference_no'   => $this->reference_no,
            'payment_method' => $this->payment_method,
            'amount'         => AppLibrary::flatAmountFormat($this->amount),
            'file'           => $this->file,
        ];
    }
}
