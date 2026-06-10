<?php

namespace Database\Seeders;

use App\Enums\PromotionType;
use App\Enums\Status;
use App\Models\Product;
use App\Models\ProductSection;
use App\Models\ProductSectionProduct;
use App\Models\Promotion;
use Illuminate\Support\Str;
use Illuminate\Database\Seeder;
use Dipokhalder\EnvEditor\EnvEditor;

class ProductSectionTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */

    public array $promotions = [
        [
            'name' => 'Trendy Collections',
        ]
    ];

    public function run(): void
    {
        $envService = new EnvEditor();
        if ($envService->getValue('DEMO')) {
            foreach ($this->promotions as $promotion) {
                $section = ProductSection::create([
                    'name'   => $promotion['name'],
                    'slug'   => Str::slug($promotion['name']),
                    'status' => Status::ACTIVE,
                ]);

                $products = Product::select('id')->inRandomOrder()->limit(35)->get();
                foreach ($products as $product) {
                    ProductSectionProduct::create([
                        'product_section_id' => $section->id,
                        'product_id'         => $product->id,
                    ]);
                }
            }
        }
    }
}
