<?php

namespace App\Http\Resources;


use App\Libraries\AppLibrary;
use Illuminate\Http\Resources\Json\JsonResource;

class SimpleOrderResource extends JsonResource
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
            'id'                      => $this->id,
            'order_serial_no'         => $this->order_serial_no,
            'order_datetime'          => AppLibrary::datetime($this->order_datetime),
            "total_amount_price"      => AppLibrary::flatAmountFormat($this->total),
            "discount_amount_price"   => AppLibrary::flatAmountFormat($this->discount),
            "shipping_amount_price"   => AppLibrary::flatAmountFormat($this->shipping_charge),
            'payment_method'          => $this->payment_method,
            'payment_method_name'     => $this?->paymentMethod?->name,
            'payment_status'          => $this->payment_status,
            'transaction'             => new TransactionResource($this->transaction),
            'order_type'              => $this->order_type,
            'pos_payment_method_name' => trans("posPaymentMethod." . $this->pos_payment_method),
        ];
    }
}
