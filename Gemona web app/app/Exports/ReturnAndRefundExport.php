<?php

namespace App\Exports;

use App\Libraries\AppLibrary;
use App\Http\Requests\PaginateRequest;
use App\Services\ReturnAndRefundService;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\FromCollection;

class ReturnAndRefundExport implements FromCollection, WithHeadings
{

    public ReturnAndRefundService $returnAndRefundService;
    public PaginateRequest $request;

    public function __construct(ReturnAndRefundService $returnAndRefundService, $request)
    {
        $this->returnAndRefundService = $returnAndRefundService;
        $this->request      = $request;
    }

    public function collection()
    {
        $orderArray  = [];
        $ordersArray = $this->returnAndRefundService->list($this->request);

        foreach ($ordersArray as $order) {
            $orderArray[] = [
                $order->order_serial_no,
                optional($order->user)->name,
                AppLibrary::flatAmountFormat($order?->returnProducts->sum('return_price')),
                AppLibrary::datetime($order->created_at),
                trans('returnOrderStatus.' . $order->status),
            ];
        }
        return collect($orderArray);
    }

    public function headings(): array
    {
        return [
            trans('all.label.order_serial_no'),
            trans('all.label.customer'),
            trans('all.label.total_return_price'),
            trans('all.label.date'),
            trans('all.label.status'),
        ];
    }
}