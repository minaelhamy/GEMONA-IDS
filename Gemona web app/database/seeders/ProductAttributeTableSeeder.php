<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\ProductAttribute;
use Dipokhalder\EnvEditor\EnvEditor;

class ProductAttributeTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public array $fashionAttributes = [
        'Color',
        'Size'
    ];

    public function run()
    {
        $envService = new EnvEditor();
        if ($envService->getValue('DEMO') && $envService->getValue('DISPLAY_TYPE') == 'fashion') {
            foreach ($this->fashionAttributes as $fashionAttribute) {
                ProductAttribute::create([
                    'name'   => $fashionAttribute,
                ]);
            }
        }
    }
}
