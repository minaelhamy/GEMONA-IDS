<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use App\Http\Requests\PaginateRequest;
use App\Libraries\AppLibrary;
use App\Models\ReturnOrder;
use App\Services\ReturnOrderService;
use Maatwebsite\Excel\Concerns\WithHeadings;

class ReturnOrdersExport implements FromCollection,WithHeadings
{
    

    public ReturnOrderService $returnOrderService;
    public PaginateRequest $request;

    public function __construct(ReturnOrderService $returnOrderService, $request)
    {
        $this->returnOrderService = $returnOrderService;
        $this->request         = $request;
    }

    /**
    * @return \Illuminate\Support\Collection
    */
    public function collection()
    {
        $purchaseArr = [];
        $purchasesArr = $this->returnOrderService->list($this->request);

        foreach ($purchasesArr as $returnOrder) {
            $purchaseArr[] = [
                $returnOrder->user->name,
                AppLibrary::date($returnOrder->date),
                $returnOrder->reference_no,
                $returnOrder->total,
                $returnOrder->reason
            ];
        }
        return collect($purchaseArr);
    }
    public function headings(): array
    {
        return [
            trans('all.label.customer'),
            trans('all.label.date'),
            trans('all.label.reference_no'),
            trans('all.label.total'),
            trans('all.label.reason'),
        ];
    }
}
