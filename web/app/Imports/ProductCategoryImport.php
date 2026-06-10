<?php

namespace App\Imports;

use App\Enums\Status;
use App\Libraries\AppEnum;
use Illuminate\Support\Str;
use App\Models\ProductCategory;
use Illuminate\Validation\Rule;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\Importable;
use Maatwebsite\Excel\Concerns\SkipsFailures;
use Maatwebsite\Excel\Concerns\SkipsEmptyRows;
use Maatwebsite\Excel\Concerns\SkipsOnFailure;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;

class ProductCategoryImport implements ToModel, WithHeadingRow, WithValidation, SkipsOnFailure, SkipsEmptyRows
{
    use Importable, SkipsFailures;

    public function model(array $row)
    {
        return new ProductCategory([
            'name'        => $this->sanitizeInput($row['name']),
            'slug'        => Str::slug($this->sanitizeInput($row['name'])) . rand(1, 1000),
            'description' => $this->sanitizeInput($row['description'] ?? null),
            'status'      => AppEnum::status($row['status']),
            'parent_id'   => AppEnum::getCategoryId($this->sanitizeInput($row['parent_category'] ?? null)),
        ]);
    }

    public function rules(): array
    {
        return [
            'name'        => [
                'required',
                'string',
                'max:190',
                Rule::unique("product_categories", "name")
            ],
            'description'     => ['nullable', 'string', 'max:900'],
            'parent_category' => ['nullable', 'string', 'max:190'],
            'status'          => ['required', 'string', 'max:190'],
        ];
    }

    private function sanitizeInput($value)
    {
        return mb_convert_encoding(trim($value), 'UTF-8', 'UTF-8');
    }
}