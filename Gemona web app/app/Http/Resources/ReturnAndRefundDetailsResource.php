<?php

namespace App\Http\Resources;

use App\Enums\Ask;
use App\Libraries\AppLibrary;
use Illuminate\Http\Resources\Json\JsonResource;

class ReturnAndRefundDetailsResource extends JsonResource
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
            'return_reason_name'          => $this?->returnReason?->title,
            'note'                        => $this->note === null ? '' : $this->note,
            'reject_reason'               => $this->reject_reason === null ? '' : $this->reject_reason,
            'order_id'                    => $this->order_id,
            'order_datetime'              => AppLibrary::datetime($this?->order?->order_datetime),
            'return_datetime'             => AppLibrary::datetime($this->created_at),
            'return_total_currency_price' => AppLibrary::currencyAmountFormat($this?->returnProducts->sum('return_price')),
            "return_total_price"          => AppLibrary::flatAmountFormat($this?->returnProducts->sum('return_price')),
            'user_id'                     => $this->user_id,
            'order_serial_no'             => $this->order_serial_no,
            'status'                      => $this->status,
            "images"                      => $this->images,
            'user'                        => new UserResource($this?->user),
            'return_products'             => ReturnAndRefundProductResource::collection($this?->returnProducts),
        ];
    }
}
