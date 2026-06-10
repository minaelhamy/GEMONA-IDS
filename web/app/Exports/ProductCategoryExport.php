<?php

namespace App\Exports;

use App\Services\ProductCategoryService;
use App\Http\Requests\PaginateRequest;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\FromCollection;

class ProductCategoryExport implements FromCollection, WithHeadings
{

    public ProductCategoryService $productCategory;
    public PaginateRequest $request;

    public function __construct(ProductCategoryService $productCategory, $request)
    {
        $this->productCategory = $productCategory;
        $this->request            = $request;
    }

    public function collection(): \Illuminate\Support\Collection
    {
        $productCategoryArray = [];
        $productCategorys     = $this->productCategory->list($this->request);

        foreach ($productCategorys as $productCategory) {
            $productCategoryArray[] = [
                $productCategory->name,
                optional($productCategory->parent_category)->name,
                trans('statuse.' . $productCategory->status),
            ];
        }
        return collect($productCategoryArray);
    }

    public function headings(): array
    {
        return [
            trans('all.label.name'),
            trans('all.label.parent_category'),
            trans('all.label.status')
        ];
    }
}