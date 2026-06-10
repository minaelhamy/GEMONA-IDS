<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use App\Http\Resources\ProductWithVariationResource;
use App\Libraries\AppLibrary;

class PurchaseDetailsResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id'                      => $this->id,
            'supplier_id'             => $this->supplier_id,
            'supplier_name'           => $this->supplier->name,
            'date'                    => $this->date,
            'converted_date'          => AppLibrary::datetime($this->date),
            'reference_no'            => $this->reference_no,
            'status'                  => $this->status,
            'subtotal'                => $this->subtotal,
            'subtotal_currency_price' => AppLibrary::currencyAmountFormat($this->subtotal),
            'tax'                     => $this->tax,
            'tax_currency_price'      => AppLibrary::currencyAmountFormat($this->tax),
            'discount'                => $this->discount,
            'discount_currency_price' => AppLibrary::currencyAmountFormat($this->discount),
            'total'                   => $this->total,
            'total_currency_price'    => AppLibrary::currencyAmountFormat($this->total),
            'note'                    => $this->note,
            'supplier'                => $this->supplier,
            'creator'                 => $this->creator,
            'created_at'              => AppLibrary::datetime($this->created_at),
            'file'                    => $this->file,
            'products'                => ProductWithVariationResource::collection($this->stocks),
            'due_payment'             => (floatval($this->total) - floatval($this?->purchasePayment?->sum('amount'))),
        ];
    }
}
