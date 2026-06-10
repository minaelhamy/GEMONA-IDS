<?php

namespace Database\Seeders;

use App\Enums\AddressType;
use App\Enums\Ask;
use App\Enums\OrderStatus;
use App\Enums\OrderType;
use App\Enums\PaymentStatus;
use App\Enums\Source;
use App\Enums\Status;
use App\Models\OrderAddress;
use App\Models\OrderCoupon;
use App\Models\ProductVariation;
use App\Models\ReturnOrder;
use App\Models\Stock;
use App\Models\StockTax;
use App\Models\Transaction;
use Dipokhalder\EnvEditor\EnvEditor;
use Illuminate\Database\Seeder;
use App\Models\Order;

class ReturnOrderTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $envService = new EnvEditor();
        if ($envService->getValue('DEMO') && $envService->getValue('DISPLAY_TYPE') == 'fashion') {
            ReturnOrder::insert([
                [
                    'user_id'      => 3,
                    'date'         => now(),
                    'reference_no' => '12313131',
                    'subtotal'     => 130.000000,
                    'tax'          => 23.500000,
                    'discount'     => 0.000000,
                    'total'        => 153.500000,
                    'reason'       => '<p>Ordered the wrong product or size</p>',
                    'created_at'   => now(),
                    'updated_at'   => now()
                ]
            ]);

            Stock::insert([
                [
                    'product_id'      => 3,
                    'model_type'      => ReturnOrder::class,
                    'model_id'        => 1,
                    'item_type'       => ProductVariation::class,
                    'item_id'         => 18,
                    'variation_names' => 'White | S',
                    'sku'             => '8452377',
                    'price'           => 80.000000,
                    'quantity'        => 1,
                    'discount'        => 0.000000,
                    'tax'             => 16.000000,
                    'subtotal'        => 80.000000,
                    'total'           => 96.000000,
                    'status'          => Status::ACTIVE,
                    'created_at'      => now(),
                    'updated_at'      => now()
                ],
                [
                    'product_id'      => 38,
                    'model_type'      => ReturnOrder::class,
                    'model_id'        => 1,
                    'item_type'       => ProductVariation::class,
                    'item_id'         => 303,
                    'variation_names' => 'Black | M',
                    'sku'             => '24111633',
                    'price'           => 50.000000,
                    'quantity'        => 1,
                    'discount'        => 0.000000,
                    'tax'             => 7.500000,
                    'subtotal'        => 50.000000,
                    'total'           => 57.500000,
                    'status'          => Status::ACTIVE,
                    'created_at'      => now(),
                    'updated_at'      => now()
                ]
            ]);

            StockTax::insert([
                [
                    'stock_id'   => 653,
                    'product_id' => 3,
                    'tax_id'     => 4,
                    'name'       => 'VAT-20',
                    'code'       => 'VAT-20%',
                    'tax_rate'   => 20.000000,
                    'tax_amount' => 16.000000,
                    'created_at' => now(),
                    'updated_at' => now()
                ],
                [
                    'stock_id'   => 654,
                    'product_id' => 38,
                    'tax_id'     => 2,
                    'name'       => 'VAT-5',
                    'code'       => 'VAT-5%',
                    'tax_rate'   => 5.000000,
                    'tax_amount' => 2.500000,
                    'created_at' => now(),
                    'updated_at' => now()
                ],
                [
                    'stock_id'   => 654,
                    'product_id' => 38,
                    'tax_id'     => 3,
                    'name'       => 'VAT-10',
                    'code'       => 'VAT-10%',
                    'tax_rate'   => 10.000000,
                    'tax_amount' => 5.000000,
                    'created_at' => now(),
                    'updated_at' => now()
                ],
            ]);
        }
    }
}
