<?php

namespace App\Http\Resources;


use App\Libraries\AppLibrary;
use Illuminate\Http\Resources\Json\JsonResource;

class ReturnAndRefundResource extends JsonResource
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
            'id'                          => $this->id,
            'return_reason_id'            => $this->return_reason_id,
            'note'                        => $this->note === null ? '' : $this->note,
            'order_id'                    => $this->order_id,
            'order_datetime'              => AppLibrary::datetime($this?->order?->order_datetime),
            'return_datetime'             => AppLibrary::datetime($this->created_at),
            'return_items'                => $this?->returnProducts->count(),
            'return_total_currency_price' => AppLibrary::currencyAmountFormat($this?->returnProducts->sum('return_price')),
            "return_total_price"          => AppLibrary::flatAmountFormat($this?->returnProducts->sum('return_price')),
            'user_id'                     => $this->user_id,
            'user_name'                   => $this?->user?->name,
            'order_serial_no'             => $this->order_serial_no,
            'status'                      => $this->status,
        ];
    }
}
