<?php

namespace Database\Seeders;

use App\Models\Product;
use App\Enums\BarcodeType;
use Illuminate\Database\Seeder;
use Picqer\Barcode\BarcodeGeneratorJPG;

class ProductTableSeederVersionTwo extends Seeder
{


    public function run(): void
    {
        $products = Product::all();
        foreach ($products as $product) {
            $barcode_id = $product->barcode_id;
            $generator     = new BarcodeGeneratorJPG();
            $barcode_value = str_pad($product->sku, $barcode_id === BarcodeType::EAN_13 ? 12 : 11, '0', STR_PAD_LEFT);
            $barcode       = $generator->getBarcode($barcode_value, $barcode_id === BarcodeType::EAN_13 ?  $generator::TYPE_EAN_13 : $generator::TYPE_UPC_A);
            $tempFilePath  = storage_path('app/public/barcode.jpg');
            file_put_contents($tempFilePath, $barcode);
            $product->addMedia($tempFilePath)->toMediaCollection('product-barcode');
        }
    }
}
