<?php

namespace App\Http\Resources;


use Illuminate\Http\Resources\Json\JsonResource;

class ProductsReportOverviewResource extends JsonResource
{
    public $info;

    public function __construct($info)
    {
        parent::__construct($info);
        $this->info = $info;
    }

    public function toArray($request)
    {
        return [
            "total_products"      => $this->info['total_products'],
            "total_categories"    => $this->info['total_categories'],
            "total_sold_quantity" => $this->info['total_sold_quantity'],
        ];
    }
}
