<?php

namespace App\Exports;

use App\Services\ProductService;
use App\Http\Requests\PaginateRequest;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\FromCollection;
use App\Libraries\AppLibrary;

class ProductExport implements FromCollection, WithHeadings
{

    public ProductService $productService;
    public PaginateRequest $request;

    public function __construct(ProductService $productService, $request)
    {
        $this->productService = $productService;
        $this->request            = $request;
    }

    public function collection(): \Illuminate\Support\Collection
    {
        $productArray = [];
        $products     = $this->productService->list($this->request);

        foreach ($products as $product) {
            $productArray[] = [
                $product->name,
                $product->sku,
                $product->category?->name,
                $product->brand?->name,
                $product->barcode?->name,
                $product->unit?->name,
                $this->getTaxNames($product->productTaxes),
                AppLibrary::flatAmountFormat($product->buying_price),
                AppLibrary::flatAmountFormat($product->selling_price),
                trans('ask.' . $product->can_purchasable),
                trans('ask.' . $product->show_stock_out),
                $product->maximum_purchase_quantity,
                $product->low_stock_quantity_warning,
                $product->weight,
                trans('ask.' . $product->refundable),
                trans('statuse.' . $product->status),
            ];
        }
        return collect($productArray);
    }

    public function headings(): array
    {
        return [
            trans('all.label.name'),
            trans('all.label.sku'),
            trans('all.label.product_category_id'),
            trans('all.label.product_brand_id'),
            trans('all.label.barcode_id'),
            trans('all.label.unit_id'),
            trans('all.label.tax'),
            trans('all.label.buying_price'),
            trans('all.label.selling_price'),
            trans('all.label.purchasable'),
            trans('all.label.show_stock_out'),
            trans('all.label.maximum_purchase_quantity'),
            trans('all.label.low_stock_quantity_warning'),
            trans('all.label.weight'),
            trans('all.label.refundable'),
            trans('all.label.status')
        ];
    }

    public function getTaxNames ($taxes)
    {
        $taxNames = '';
        $i = 0;
        foreach ($taxes as $tax) {
          $taxNames .= $i !=0 ? ", ".$tax->tax->name : $tax->tax->name;
          $i++;
        }
        return $taxNames;
    }
}
