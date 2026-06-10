<?php

namespace Database\Seeders;

use App\Enums\Status;
use Illuminate\Support\Str;
use App\Models\ProductCategory;
use Illuminate\Database\Seeder;
use Dipokhalder\EnvEditor\EnvEditor;

class ProductCategoryTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */


    public array $fashionCategories = [
        [
            'name'     => 'Men',
            'children' => [
                [
                    'name'     => 'Clothing',
                    'children' => [
                        ['name' => 'Hoodies & Sweatshirts'],
                        ['name' => 'Jackets & Vests'],
                        ['name' => 'Pants & Tights'],
                        ['name' => 'Shorts'],
                        ['name' => 'Tops & T-shirts']
                    ]
                ],
                [
                    'name'     => 'Shoes',
                    'children' => [
                        ['name' => 'Basket Ball'],
                        ['name' => 'Running'],
                        ['name' => 'Sandals & Slides'],
                        ['name' => 'Sneakers'],
                        ['name' => 'Soccer'],
                    ]
                ],
                [
                    'name'     => 'Accessories',
                    'children' => [
                        ['name' => 'Bags & Backpacks'],
                        ['name' => 'Hat & Beanies'],
                        ['name' => 'Socks'],
                        ['name' => 'Underwear']
                    ]
                ],
            ],
        ],
        [
            'name'     => 'Women',
            'children' => [
                [
                    'name'     => 'Clothing',
                    'children' => [
                        ['name' => 'Dresses & Skirts'],
                        ['name' => 'Hoodies & Sweatshirts'],
                        ['name' => 'Pants'],
                        ['name' => 'Tights & Leggings'],
                        ['name' => 'Tops & T-shirts']
                    ]
                ],
                [
                    'name'     => 'Shoes',
                    'children' => [
                        ['name' => 'Running'],
                        ['name' => 'Sneakers'],
                        ['name' => 'Training & Gym']
                    ]
                ],
                [
                    'name'     => 'Accessories',
                    'children' => [
                        ['name' => 'Bags & Backpacks'],
                        ['name' => 'Hats'],
                        ['name' => 'Socks']
                    ]
                ]
            ]
        ],
        [
            'name'     => 'Juniors',
            'children' => [
                [
                    'name'     => 'Clothing',
                    'children' => [
                        ['name' => 'Hoodies & Sweatshirts'],
                        ['name' => 'Shorts'],
                        ['name' => 'Tops & T-shirts']
                    ]
                ],
                [
                    'name'     => 'Shoes',
                    'children' => [
                        ['name' => 'Basket Ball'],
                        ['name' => 'Running'],
                        ['name' => 'Sneakers']
                    ]
                ],
                [
                    'name'     => 'Accessories',
                    'children' => [
                        ['name' => 'Bags & Backpacks'],
                        ['name' => 'Hats'],
                    ]
                ]
            ]
        ]
    ];

    public function run(): void
    {
        $envService = new EnvEditor();
        if ($envService->getValue('DEMO') && $envService->getValue('DISPLAY_TYPE') == 'fashion') {
            foreach ($this->fashionCategories as $fashionCategory) {
                $productCategory = ProductCategory::create([
                    'parent_id'   => NULL,
                    'name'        => $fashionCategory['name'],
                    'slug'        => Str::slug($fashionCategory['name'] . rand(1, 100)),
                    'description' => null,
                    'status'      => Status::ACTIVE,
                ]);
                if (file_exists(public_path('/images/seeder/product-category/' . env('DISPLAY_TYPE') . '/' . strtolower(str_replace(' ', '_', $fashionCategory['name'])) . '.png'))) {
                    $productCategory->addMedia(public_path('/images/seeder/product-category/' . env('DISPLAY_TYPE') . '/' . strtolower(str_replace(' ', '_', $fashionCategory['name'])) . '.png'))->preservingOriginal()->toMediaCollection('product-category');
                }

                if (isset($fashionCategory['children']) && count($fashionCategory['children']) > 0) {
                    $this->nested($fashionCategory['children'], $productCategory->id);
                }
            }
        }
    }

    public function nested($arrays, $id = null): void
    {
        foreach ($arrays as $array) {
            $productCategory = ProductCategory::create([
                'parent_id'   => $id,
                'name'        => $array['name'],
                'slug'        => Str::slug($array['name'] . rand(101, 500)).rand(100, 200),
                'description' => null,
                'status'      => Status::ACTIVE,
            ]);
            if (file_exists(public_path('/images/seeder/product-category/' . env('DISPLAY_TYPE') . '/' . strtolower(str_replace(' ', '_', $array['name'])) . '.png'))) {
                $productCategory->addMedia(public_path('/images/seeder/product-category/' . env('DISPLAY_TYPE') . '/' . strtolower(str_replace(' ', '_', $array['name'])) . '.png'))->preservingOriginal()->toMediaCollection('product-category');
            }
            if (isset($array['children']) && count($array) > 0) {
                $this->nested($array['children'], $productCategory->id);
            }
        }
    }
}
