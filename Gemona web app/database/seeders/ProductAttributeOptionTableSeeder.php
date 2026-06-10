<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Dipokhalder\EnvEditor\EnvEditor;
use App\Models\ProductAttributeOption;

class ProductAttributeOptionTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public array $fashionAttributeOptions = [
        [
            'product_attribute' => 1,
            'name'              => 'White',
        ],
        [
            'product_attribute' => 1,
            'name'              => 'Black',
        ],
        [
            'product_attribute' => 1,
            'name'              => 'Grey',
        ],
        [
            'product_attribute' => 1,
            'name'              => 'Red',
        ],
        [
            'product_attribute' => 1,
            'name'              => 'Blue',
        ],
        [
            'product_attribute' => 2,
            'name'              => 'S',
        ],
        [
            'product_attribute' => 2,
            'name'              => 'M',
        ],
        [
            'product_attribute' => 2,
            'name'              => 'L',
        ],
        [
            'product_attribute' => 2,
            'name'              => 'XL',
        ],
        [
            'product_attribute' => 2,
            'name'              => 'XXL',
        ],
    ];

    public function run()
    {
        $envService = new EnvEditor();
        if ($envService->getValue('DEMO') && $envService->getValue('DISPLAY_TYPE') == 'fashion') {
            foreach ($this->fashionAttributeOptions as $fashionAttributeOption) {
                ProductAttributeOption::create([
                    'product_attribute_id' => $fashionAttributeOption['product_attribute'],
                    'name'                 => $fashionAttributeOption['name'],
                ]);
            }
        }
    }
}
