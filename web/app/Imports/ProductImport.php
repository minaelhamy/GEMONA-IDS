<?php

namespace App\Imports;

use App\Models\Barcode;
use App\Models\Product;
use App\Libraries\AppEnum;
use Illuminate\Support\Str;
use App\Libraries\AppLibrary;
use Illuminate\Validation\Rule;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\Importable;
use Maatwebsite\Excel\Concerns\SkipsFailures;
use Maatwebsite\Excel\Concerns\SkipsEmptyRows;
use Maatwebsite\Excel\Concerns\SkipsOnFailure;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;

class ProductImport implements ToModel, WithHeadingRow, WithValidation, SkipsOnFailure, SkipsEmptyRows
{
    use Importable, SkipsFailures;

    public function model(array $row)
    {
        return new Product([
            'name'                       => $this->sanitizeInput($row['name']),
            'slug'                       => Str::slug($this->sanitizeInput($row['name'])) . rand(1, 1000),
            'sku'                        => AppLibrary::sku(substr(floor(microtime(true) * 1000), -4) . rand(1, max: 999)),
            'description'                => $this->sanitizeInput($row['description'] ?? null),
            'product_category_id'        => AppEnum::getCategoryId($row['category']),
            'barcode_id'                 => Barcode::first()->id,
            'buying_price'               => $row['buying_price'] ?? 0,
            'selling_price'              => $row['selling_price'] ?? 0,
            'status'                     => AppEnum::status($row['status']),
            'can_purchasable'            => AppEnum::ask($row['purchasable']),
            'show_stock_out'             => AppEnum::ask($row['show_stock_out']),
            'refundable'                 => AppEnum::ask($row['refundable']),
            'maximum_purchase_quantity'  => $row['maximum_purchase_quantity'] ?? 1,
            'low_stock_quantity_warning' => $row['low_stock_quantity_warning'] ?? 1,
        ]);
    }

    public function rules(): array
    {
        return [
            'name' => [
                'required',
                'string',
                'max:190',
                Rule::unique("products", "name")->whereNull('deleted_at')
            ],
            'description'                => ['nullable', 'string', 'max:5000'],
            'buying_price'               => ['required'],
            'selling_price'              => ['required'],
            'status'                     => ['required', 'string'],
            'purchasable'                => ['required', 'string'],
            'show_stock_out'             => ['required', 'string'],
            'refundable'                 => ['required', 'string'],
            'maximum_purchase_quantity'  => ['required', 'numeric', 'max_digits:10'],
            'low_stock_quantity_warning' => ['required', 'numeric', 'max_digits:10'],
        ];
    }

    private function sanitizeInput($value)
    {
        return mb_convert_encoding(trim($value), 'UTF-8', 'UTF-8');
    }
}
