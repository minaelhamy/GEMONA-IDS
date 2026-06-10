<?php

namespace App\Exports;

use App\Libraries\AppLibrary;
use App\Services\ProductService;
use App\Http\Requests\PaginateRequest;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\FromCollection;

class ProductsReportExport implements FromCollection, WithHeadings
{

    public ProductService $productService;
    public PaginateRequest $request;

    public function __construct(ProductService $productService, $request)
    {
        $this->productService = $productService;
        $this->request     = $request;
    }

    public function collection(): \Illuminate\Support\Collection
    {
        $productsReportArray  = [];
        $productsReportsArray = $this->productService->productReport($this->request);

        $total = 0;
        foreach ($productsReportsArray as $product) {
            $total              += $product?->productOrders->count();
            $productsReportArray[] = [
                $product->name,
                optional($product->category)->name,
                abs($product?->productOrders->sum('quantity'))
            ];
        }
        $productsReportArray[] = [
            trans('all.label.total'),
            '',
            $total
        ];
        return collect($productsReportArray);
    }

    public function headings(): array
    {
        return [
            trans('all.label.name'),
            trans('all.label.product_category_id'),
            trans('all.label.sold_quantity'),
        ];
    }
}
