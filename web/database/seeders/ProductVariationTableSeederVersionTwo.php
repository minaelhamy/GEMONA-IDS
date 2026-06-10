<?php

namespace Database\Seeders;

use App\Enums\BarcodeType;
use App\Models\ProductVariation;
use Illuminate\Database\Seeder;
use Picqer\Barcode\BarcodeGeneratorJPG;

class ProductVariationTableSeederVersionTwo extends Seeder
{


    public function run(): void
    {
        $productVariations = ProductVariation::all();
        foreach ($productVariations as $productVariation) {
            if (!empty($productVariation->sku)) {
                $barcode_id    = $productVariation?->product->barcode_id;
                $generator     = new BarcodeGeneratorJPG();
                $barcode_value = str_pad($productVariation->sku, $barcode_id === BarcodeType::EAN_13 ? 12 : 11, '0', STR_PAD_LEFT);
                $barcode       = $generator->getBarcode($barcode_value, $barcode_id === BarcodeType::EAN_13 ?  $generator::TYPE_EAN_13 : $generator::TYPE_UPC_A);
                $tempFilePath  = storage_path('app/public/barcode.jpg');
                file_put_contents($tempFilePath, $barcode);
                $productVariation->addMedia($tempFilePath)->toMediaCollection('product-variation-barcode');
            }
        }
    }
}
