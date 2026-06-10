<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use App\Http\Requests\PaginateRequest;
use App\Libraries\AppLibrary;
use App\Models\Damage;
use App\Services\DamageService;
use Maatwebsite\Excel\Concerns\WithHeadings;

class DamageExport implements FromCollection,WithHeadings
{

    public DamageService $damageService;
    public PaginateRequest $request;

    public function __construct(DamageService $damageService, $request)
    {
        $this->damageService = $damageService;
        $this->request         = $request;
    }
    /**
    * @return \Illuminate\Support\Collection
    */
    public function collection()
    {
        $damageArr = [];
        $damagesArr = $this->damageService->list($this->request);

        foreach ($damagesArr as $returnOrder) {
            $damageArr[] = [
                AppLibrary::date($returnOrder->date),
                $returnOrder->reference_no,
                $returnOrder->total,
                $returnOrder->note
            ];
        }
        return collect($damageArr);
    }
    public function headings(): array
    {
        return [
            trans('all.label.date'),
            trans('all.label.reference_no'),
            trans('all.label.total'),
            trans('all.label.note'),
        ];
    }
}
