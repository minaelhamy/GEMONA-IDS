<?php

namespace Database\Seeders;

use App\Enums\PromotionType;
use App\Enums\Status;
use App\Models\Product;
use App\Models\ProductCategory;
use App\Models\Promotion;
use App\Models\PromotionProduct;
use App\Services\ProductService;
use Dipokhalder\EnvEditor\Exceptions\EnvException;
use Illuminate\Support\Str;
use Illuminate\Database\Seeder;
use Dipokhalder\EnvEditor\EnvEditor;

class PromotionTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */

    public array $fashionPromotions = [
        [
            'name'          => 'Winter exclusive for man',
            'type'          => PromotionType::SMALL,
            'category_id' => 1,
        ],
        [
            'name'          => 'Winter exclusive for woman',
            'type'          => PromotionType::SMALL,
            'category_id' => 19,
        ],
        [
            'name'          => 'Winter exclusive for kids',
            'type'          => PromotionType::SMALL,
            'category_id' => 34,
        ],
        [
            'name'          => 'Winter collection',
            'type'          => PromotionType::BIG,
            'category_id' => null,
        ],
    ];

    /**
     * @throws EnvException
     */
    public function run(): void
    {
        $envService = new EnvEditor();
        if ($envService->getValue('DEMO') && $envService->getValue('DISPLAY_TYPE') == 'fashion') {
            $productService = new ProductService();

            foreach ($this->fashionPromotions as $fashionPromotion) {
                $fashionPromotionObject = Promotion::create([
                    'name'   => $fashionPromotion['name'],
                    'slug'   => Str::slug($fashionPromotion['name']),
                    'type'   => $fashionPromotion['type'],
                    'status' => Status::ACTIVE,
                ]);

                if (file_exists(public_path('/images/seeder/promotion/' . env('DISPLAY_TYPE') . '/' . strtolower(str_replace(' ', '_', $fashionPromotion['name'])) . '.png'))) {
                    $fashionPromotionObject->addMedia(public_path('/images/seeder/promotion/' . env('DISPLAY_TYPE') . '/' . strtolower(str_replace(' ', '_', $fashionPromotion['name'])) . '.png'))->preservingOriginal()->toMediaCollection('promotion');
                }

                if($fashionPromotion['category_id']) {
                    $category = ProductCategory::where(['id' =>  $fashionPromotion['category_id']])->first();
                    $products = $productService->ancestorCategoryWiseProducts($category, 35);
                } else {
                    $products = Product::select('id')->inRandomOrder()->limit(35)->get();
                }

                foreach ($products as $product) {
                    PromotionProduct::create([
                        'promotion_id' => $fashionPromotionObject->id,
                        'product_id'   => $product->id,
                    ]);
                }
            }
        }
    }
}
