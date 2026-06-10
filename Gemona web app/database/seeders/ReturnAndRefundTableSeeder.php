<?php

namespace Database\Seeders;


use App\Enums\ReturnOrderStatus;
use App\Models\ReturnAndRefund;
use App\Models\ReturnAndRefundProduct;
use Dipokhalder\EnvEditor\EnvEditor;
use Illuminate\Database\Seeder;

class ReturnAndRefundTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run(): void
    {
        $envService = new EnvEditor();
        if ($envService->getValue('DEMO') && $envService->getValue('DISPLAY_TYPE') == 'fashion') {
            ReturnAndRefund::insert([
                [
                    'return_reason_id' => 1,
                    'note'             => 'Ordered the wrong product or size',
                    'reject_reason'    => null,
                    'order_id'         => 1,
                    'user_id'          => 3,
                    'order_serial_no'  => 412231,
                    'status'           => ReturnOrderStatus::ACCEPT,
                    'created_at'       => now(),
                    'updated_at'       => now()
                ]
            ]);

            ReturnAndRefundProduct::insert([
                [
                    'return_and_refund_id' => 1,
                    'product_id'           => 3,
                    'variation_id'         => 18,
                    'variation_names'      => 'White | S',
                    'quantity'             => 1,
                    'price'                => 60.000000,
                    'total'                => 72.000000,
                    'return_price'         => 63.000000,
                    'user_id'              => 3,
                    'created_at'           => now(),
                    'updated_at'           => now()
                ],
                [
                    'return_and_refund_id' => 1,
                    'product_id'           => 38,
                    'variation_id'         => 303,
                    'variation_names'      => 'Black | M',
                    'quantity'             => 1,
                    'price'                => 65.000000,
                    'total'                => 74.750000,
                    'return_price'         => 65.000000,
                    'user_id'              => 3,
                    'created_at'           => now(),
                    'updated_at'           => now()
                ]
            ]);
        }
    }
}
