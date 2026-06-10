<?php

namespace App\Exports;

use App\Http\Requests\PaginateRequest;
use App\Libraries\AppLibrary;
use App\Models\Purchase;
use App\Services\PurchaseService;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class PurchasesExport implements FromCollection,WithHeadings
{

    public PurchaseService $purchaseService;
    public PaginateRequest $request;

    public function __construct(PurchaseService $purchaseService, $request)
    {
        $this->purchaseService = $purchaseService;
        $this->request         = $request;
    }

    /**
    * @return \Illuminate\Support\Collection
    */
    public function collection()
    {
        $purchaseArr = [];
        $purchasesArr = $this->purchaseService->list($this->request);

        foreach ($purchasesArr as $purchase) {
            $purchaseArr[] = [
                $purchase->supplier->name,
                AppLibrary::date($purchase->date),
                $purchase->reference_no,
                $purchase->status,
                $purchase->total,
                $purchase->note
            ];
        }
        return collect($purchaseArr);
    }
    public function headings(): array
    {
        return [
            trans('all.label.supplier'),
            trans('all.label.date'),
            trans('all.label.reference_no'),
            trans('all.label.status'),
            trans('all.label.total'),
            trans('all.label.note'),
        ];
    }
}
