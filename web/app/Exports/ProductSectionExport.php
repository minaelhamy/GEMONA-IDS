<?php

namespace App\Exports;

use App\Http\Requests\PaginateRequest;
use App\Services\ProductSectionService;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\FromCollection;

class ProductSectionExport implements FromCollection, WithHeadings
{

    public ProductSectionService $productSectionService;
    public PaginateRequest $request;

    public function __construct(ProductSectionService $productSectionService, $request)
    {
        $this->productSectionService = $productSectionService;
        $this->request      = $request;
    }

    public function collection()
    {
        $productSectionArray  = [];
        $productSectionsArray = $this->productSectionService->list($this->request);

        foreach ($productSectionsArray as $productSection) {
            $productSectionArray[] = [
                $productSection->name,
                $productSection->slug,
                trans('statuse.' . $productSection->status),
            ];
        }
        return collect($productSectionArray);
    }

    public function headings(): array
    {
        return [
            trans('all.label.name'),
            trans('all.label.slug'),
            trans('all.label.status'),
        ];
    }
}
