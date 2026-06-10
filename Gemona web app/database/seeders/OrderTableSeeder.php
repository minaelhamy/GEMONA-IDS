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
use App\Models\Stock;
use App\Models\StockTax;
use App\Models\Transaction;
use Dipokhalder\EnvEditor\EnvEditor;
use Illuminate\Database\Seeder;
use App\Models\Order;

class OrderTableSeeder extends Seeder
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
            Order::insert([
                [
                    'order_serial_no' => date('dmy') . '1',
                    'user_id'         => 3,
                    'subtotal'        => 389.000000,
                    'tax'             => 74.550000,
                    'discount'        => 58.350000,
                    'shipping_charge' => 10.000000,
                    'total'           => 415.200000,
                    'order_type'      => OrderType::DELIVERY,
                    'order_datetime'  => now(),
                    'payment_method'  => 3,
                    'payment_status'  => PaymentStatus::PAID,
                    'status'          => OrderStatus::DELIVERED,
                    'active'          => Ask::YES,
                    'source'          => Source::WEB,
                    'created_at'      => now(),
                    'updated_at'      => now()
                ],
                [
                    'order_serial_no' => date('dmy') . '2',
                    'user_id'         => 3,
                    'subtotal'        => 590.000000,
                    'tax'             => 120.000000,
                    'discount'        => 0.000000,
                    'shipping_charge' => 10.000000,
                    'total'           => 720.000000,
                    'order_type'      => OrderType::DELIVERY,
                    'order_datetime'  => now(),
                    'payment_method'  => 4,
                    'payment_status'  => PaymentStatus::PAID,
                    'status'          => OrderStatus::DELIVERED,
                    'active'          => Ask::YES,
                    'source'          => Source::WEB,
                    'created_at'      => now(),
                    'updated_at'      => now()
                ],
                [
                    'order_serial_no' => date('dmy') . '3',
                    'user_id'         => 3,
                    'subtotal'        => 240.000000,
                    'tax'             => 28.000000,
                    'discount'        => 0.000000,
                    'shipping_charge' => 10.000000,
                    'total'           => 278.000000,
                    'order_type'      => OrderType::DELIVERY,
                    'order_datetime'  => now(),
                    'payment_method'  => 1,
                    'payment_status'  => PaymentStatus::UNPAID,
                    'status'          => OrderStatus::PENDING,
                    'active'          => Ask::YES,
                    'source'          => Source::WEB,
                    'created_at'      => now(),
                    'updated_at'      => now()
                ]
            ]);

            Stock::insert([
                [
                    'product_id'      => 3,
                    'model_type'      => Order::class,
                    'model_id'        => 1,
                    'item_type'       => ProductVariation::class,
                    'item_id'         => 18,
                    'variation_names' => 'White | S',
                    'sku'             => '57403197',
                    'price'           => 60.000000,
                    'quantity'        => -1,
                    'discount'        => 0.000000,
                    'tax'             => 12.000000,
                    'subtotal'        => 60.000000,
                    'total'           => 72.000000,
                    'status'          => Status::ACTIVE,
                    'created_at'      => now(),
                    'updated_at'      => now()
                ],
                [
                    'product_id'      => 7,
                    'model_type'      => Order::class,
                    'model_id'        => 1,
                    'item_type'       => ProductVariation::class,
                    'item_id'         => 51,
                    'variation_names' => 'White | M',
                    'sku'             => '2014230',
                    'price'           => 64.000000,
                    'quantity'        => -1,
                    'discount'        => 0.000000,
                    'tax'             => 12.800000,
                    'subtotal'        => 64.000000,
                    'total'           => 76.800000,
                    'status'          => Status::ACTIVE,
                    'created_at'      => now(),
                    'updated_at'      => now()
                ],
                [
                    'product_id'      => 29,
                    'model_type'      => Order::class,
                    'model_id'        => 1,
                    'item_type'       => ProductVariation::class,
                    'item_id'         => 231,
                    'variation_names' => 'Black | M',
                    'sku'             => '86935574',
                    'price'           => 200.000000,
                    'quantity'        => -1,
                    'discount'        => 0.000000,
                    'tax'             => 40.000000,
                    'subtotal'        => 200.000000,
                    'total'           => 240.000000,
                    'status'          => Status::ACTIVE,
                    'created_at'      => now(),
                    'updated_at'      => now()
                ],
                [
                    'product_id'      => 38,
                    'model_type'      => Order::class,
                    'model_id'        => 1,
                    'item_type'       => ProductVariation::class,
                    'item_id'         => 303,
                    'variation_names' => 'Black | M',
                    'sku'             => '7552652',
                    'price'           => 65.000000,
                    'quantity'        => -1,
                    'discount'        => 0.000000,
                    'tax'             => 9.750000,
                    'subtotal'        => 65.000000,
                    'total'           => 74.750000,
                    'status'          => Status::ACTIVE,
                    'created_at'      => now(),
                    'updated_at'      => now()
                ],
                [
                    'product_id'      => 52,
                    'model_type'      => Order::class,
                    'model_id'        => 2,
                    'item_type'       => ProductVariation::class,
                    'item_id'         => 414,
                    'variation_names' => 'Black | S',
                    'sku'             => '2894414',
                    'price'           => 120.000000,
                    'quantity'        => -1,
                    'discount'        => 0.000000,
                    'tax'             => 24.000000,
                    'subtotal'        => 120.000000,
                    'total'           => 144.000000,
                    'status'          => Status::ACTIVE,
                    'created_at'      => now(),
                    'updated_at'      => now()
                ],
                [
                    'product_id'      => 46,
                    'model_type'      => Order::class,
                    'model_id'        => 2,
                    'item_type'       => ProductVariation::class,
                    'item_id'         => 362,
                    'variation_names' => 'White | S',
                    'sku'             => '35537382',
                    'price'           => 150.000000,
                    'quantity'        => -1,
                    'discount'        => 0.000000,
                    'tax'             => 30.000000,
                    'subtotal'        => 150.000000,
                    'total'           => 180.000000,
                    'status'          => Status::ACTIVE,
                    'created_at'      => now(),
                    'updated_at'      => now()
                ],
                [
                    'product_id'      => 62,
                    'model_type'      => Order::class,
                    'model_id'        => 2,
                    'item_type'       => ProductVariation::class,
                    'item_id'         => 490,
                    'variation_names' => 'White | S',
                    'sku'             => '23662243',
                    'price'           => 140.000000,
                    'quantity'        => -1,
                    'discount'        => 0.000000,
                    'tax'             => 21.000000,
                    'subtotal'        => 140.000000,
                    'total'           => 161.000000,
                    'status'          => Status::ACTIVE,
                    'created_at'      => now(),
                    'updated_at'      => now()
                ],
                [
                    'product_id'      => 74,
                    'model_type'      => Order::class,
                    'model_id'        => 2,
                    'item_type'       => ProductVariation::class,
                    'item_id'         => 591,
                    'variation_names' => 'Black | M',
                    'sku'             => '60493296',
                    'price'           => 180.000000,
                    'quantity'        => -1,
                    'discount'        => 0.000000,
                    'tax'             => 45.000000,
                    'subtotal'        => 180.000000,
                    'total'           => 225.000000,
                    'status'          => Status::ACTIVE,
                    'created_at'      => now(),
                    'updated_at'      => now()
                ],
                [
                    'product_id'      => 82,
                    'model_type'      => Order::class,
                    'model_id'        => 3,
                    'item_type'       => ProductVariation::class,
                    'item_id'         => 654,
                    'variation_names' => 'Black | S',
                    'sku'             => '82441735',
                    'price'           => 100.000000,
                    'quantity'        => -1,
                    'discount'        => 0.000000,
                    'tax'             => 5.000000,
                    'subtotal'        => 100.000000,
                    'total'           => 105.000000,
                    'status'          => Status::ACTIVE,
                    'created_at'      => now(),
                    'updated_at'      => now()
                ],
                [
                    'product_id'      => 99,
                    'model_type'      => Order::class,
                    'model_id'        => 3,
                    'item_type'       => ProductVariation::class,
                    'item_id'         => 790,
                    'variation_names' => 'Black | S',
                    'sku'             => '5186315',
                    'price'           => 80.000000,
                    'quantity'        => -1,
                    'discount'        => 0.000000,
                    'tax'             => 20.000000,
                    'subtotal'        => 80.000000,
                    'total'           => 100.000000,
                    'status'          => Status::ACTIVE,
                    'created_at'      => now(),
                    'updated_at'      => now()
                ],
                [
                    'product_id'      => 76,
                    'model_type'      => Order::class,
                    'model_id'        => 3,
                    'item_type'       => ProductVariation::class,
                    'item_id'         => 606,
                    'variation_names' => 'Black | S',
                    'sku'             => '61701517',
                    'price'           => 60.000000,
                    'quantity'        => -1,
                    'discount'        => 0.000000,
                    'tax'             => 3.000000,
                    'subtotal'        => 60.000000,
                    'total'           => 63.000000,
                    'status'          => Status::ACTIVE,
                    'created_at'      => now(),
                    'updated_at'      => now()
                ]
            ]);

            StockTax::insert([
                [
                    'stock_id'   => 619,
                    'product_id' => 3,
                    'tax_id'     => 5,
                    'name'       => 'VAT-20',
                    'code'       => 'VAT-20%',
                    'tax_rate'   => 20.000000,
                    'tax_amount' => 12.000000,
                    'created_at' => now(),
                    'updated_at' => now()
                ],
                [
                    'stock_id'   => 620,
                    'product_id' => 7,
                    'tax_id'     => 10,
                    'name'       => 'VAT-20',
                    'code'       => 'VAT-20%',
                    'tax_rate'   => 20.000000,
                    'tax_amount' => 12.000000,
                    'created_at' => now(),
                    'updated_at' => now()
                ],
                [
                    'stock_id'   => 621,
                    'product_id' => 29,
                    'tax_id'     => 45,
                    'name'       => 'VAT-20',
                    'code'       => 'VAT-20%',
                    'tax_rate'   => 20.000000,
                    'tax_amount' => 40.000000,
                    'created_at' => now(),
                    'updated_at' => now()
                ],
                [
                    'stock_id'   => 622,
                    'product_id' => 38,
                    'tax_id'     => 59,
                    'name'       => 'VAT-5',
                    'code'       => 'VAT-5%',
                    'tax_rate'   => 5.000000,
                    'tax_amount' => 3.250000,
                    'created_at' => now(),
                    'updated_at' => now()
                ],
                [
                    'stock_id'   => 622,
                    'product_id' => 38,
                    'tax_id'     => 60,
                    'name'       => 'VAT-10',
                    'code'       => 'VAT-10%',
                    'tax_rate'   => 10.000000,
                    'tax_amount' => 6.500000,
                    'created_at' => now(),
                    'updated_at' => now()
                ],
                [
                    'stock_id'   => 623,
                    'product_id' => 52,
                    'tax_id'     => 82,
                    'name'       => 'VAT-20',
                    'code'       => 'VAT-20%',
                    'tax_rate'   => 20.000000,
                    'tax_amount' => 24.000000,
                    'created_at' => now(),
                    'updated_at' => now()
                ],
                [
                    'stock_id'   => 624,
                    'product_id' => 46,
                    'tax_id'     => 73,
                    'name'       => 'VAT-20',
                    'code'       => 'VAT-20%',
                    'tax_rate'   => 20.000000,
                    'tax_amount' => 30.000000,
                    'created_at' => now(),
                    'updated_at' => now()
                ],
                [
                    'stock_id'   => 625,
                    'product_id' => 62,
                    'tax_id'     => 99,
                    'name'       => 'VAT-5',
                    'code'       => 'VAT-5%',
                    'tax_rate'   => 5.000000,
                    'tax_amount' => 7.000000,
                    'created_at' => now(),
                    'updated_at' => now()
                ],
                [
                    'stock_id'   => 625,
                    'product_id' => 62,
                    'tax_id'     => 100,
                    'name'       => 'VAT-10',
                    'code'       => 'VAT-10%',
                    'tax_rate'   => 10.000000,
                    'tax_amount' => 14.000000,
                    'created_at' => now(),
                    'updated_at' => now()
                ],
                [
                    'stock_id'   => 626,
                    'product_id' => 74,
                    'tax_id'     => 121,
                    'name'       => 'VAT-5',
                    'code'       => 'VAT-5%',
                    'tax_rate'   => 5.000000,
                    'tax_amount' => 9.000000,
                    'created_at' => now(),
                    'updated_at' => now()
                ],
                [
                    'stock_id'   => 626,
                    'product_id' => 74,
                    'tax_id'     => 122,
                    'name'       => 'VAT-20',
                    'code'       => 'VAT-20%',
                    'tax_rate'   => 20.000000,
                    'tax_amount' => 36.000000,
                    'created_at' => now(),
                    'updated_at' => now()
                ],
                [
                    'stock_id'   => 627,
                    'product_id' => 82,
                    'tax_id'     => 136,
                    'name'       => 'VAT-5',
                    'code'       => 'VAT-5%',
                    'tax_rate'   => 5.000000,
                    'tax_amount' => 5.000000,
                    'created_at' => now(),
                    'updated_at' => now()
                ],
                [
                    'stock_id'   => 628,
                    'product_id' => 99,
                    'tax_id'     => 165,
                    'name'       => 'VAT-5',
                    'code'       => 'VAT-5%',
                    'tax_rate'   => 5.000000,
                    'tax_amount' => 4.000000,
                    'created_at' => now(),
                    'updated_at' => now()
                ],
                [
                    'stock_id'   => 628,
                    'product_id' => 99,
                    'tax_id'     => 166,
                    'name'       => 'VAT-20',
                    'code'       => 'VAT-20%',
                    'tax_rate'   => 20.000000,
                    'tax_amount' => 16.000000,
                    'created_at' => now(),
                    'updated_at' => now()
                ],
                [
                    'stock_id'   => 629,
                    'product_id' => 76,
                    'tax_id'     => 125,
                    'name'       => 'VAT-5',
                    'code'       => 'VAT-5%',
                    'tax_rate'   => 5.000000,
                    'tax_amount' => 3.000000,
                    'created_at' => now(),
                    'updated_at' => now()
                ]
            ]);

            OrderCoupon::insert([
                [
                    'order_id'   => 1,
                    'coupon_id'  => 1,
                    'user_id'    => 3,
                    'discount'   => 97.950000,
                    'created_at' => now(),
                    'updated_at' => now()
                ]
            ]);

            OrderAddress::insert([
                [
                    'order_id'     => 1,
                    'user_id'      => 3,
                    'address_type' => AddressType::SHIPPING,
                    'full_name'    => 'Will Smith',
                    'email'        => 'customer@example.com',
                    'country_code' => '+880',
                    'phone'        => '125333344',
                    'country'      => 'Bangladesh',
                    'address'      => 'House :3, Road: 1, Block: C, Mirpur 2',
                    'state'        => 'Dhaka',
                    'city'         => 'Dhaka',
                    'zip_code'     => '1216',
                    'created_at'   => now(),
                    'updated_at'   => now()
                ],
                [
                    'order_id'     => 1,
                    'user_id'      => 3,
                    'address_type' => AddressType::BILLING,
                    'full_name'    => 'Will Smith',
                    'email'        => 'customer@example.com',
                    'country_code' => '+880',
                    'phone'        => '125333344',
                    'country'      => 'Bangladesh',
                    'address'      => 'House :3, Road: 1, Block: C, Mirpur 2',
                    'state'        => 'Dhaka',
                    'city'         => 'Dhaka',
                    'zip_code'     => '1216',
                    'created_at'   => now(),
                    'updated_at'   => now()
                ],
                [
                    'order_id'     => 2,
                    'user_id'      => 3,
                    'address_type' => AddressType::SHIPPING,
                    'full_name'    => 'Will Smith',
                    'email'        => 'customer@example.com',
                    'country_code' => '+880',
                    'phone'        => '125333344',
                    'country'      => 'Bangladesh',
                    'address'      => 'House :30, Road: 13, Block: A, Dhanmondi 32',
                    'state'        => 'Dhaka',
                    'city'         => 'Dhaka',
                    'zip_code'     => '1209',
                    'created_at'   => now(),
                    'updated_at'   => now()
                ],
                [
                    'order_id'     => 2,
                    'user_id'      => 3,
                    'address_type' => AddressType::BILLING,
                    'full_name'    => 'Will Smith',
                    'email'        => 'customer@example.com',
                    'country_code' => '+880',
                    'phone'        => '125333344',
                    'country'      => 'Bangladesh',
                    'address'      => 'House :30, Road: 13, Block: A, Dhanmondi 32',
                    'state'        => 'Dhaka',
                    'city'         => 'Dhaka',
                    'zip_code'     => '1209',
                    'created_at'   => now(),
                    'updated_at'   => now()
                ],
                [
                    'order_id'     => 3,
                    'user_id'      => 3,
                    'address_type' => AddressType::SHIPPING,
                    'full_name'    => 'Will Smith',
                    'email'        => 'customer@example.com',
                    'country_code' => '+880',
                    'phone'        => '125333344',
                    'country'      => 'Bangladesh',
                    'address'      => 'House :3, Road: 1, Block: C, Mirpur 2',
                    'state'        => 'Dhaka',
                    'city'         => 'Dhaka',
                    'zip_code'     => '1216',
                    'created_at'   => now(),
                    'updated_at'   => now()
                ],
                [
                    'order_id'     => 3,
                    'user_id'      => 3,
                    'address_type' => AddressType::BILLING,
                    'full_name'    => 'Will Smith',
                    'email'        => 'customer@example.com',
                    'country_code' => '+880',
                    'phone'        => '125333344',
                    'country'      => 'Bangladesh',
                    'address'      => 'House :3, Road: 1, Block: C, Mirpur 2',
                    'state'        => 'Dhaka',
                    'city'         => 'Dhaka',
                    'zip_code'     => '1216',
                    'created_at'   => now(),
                    'updated_at'   => now()
                ],
            ]);

            Transaction::insert([
                [
                    'sign'           => '+',
                    'order_id'       => 1,
                    'transaction_no' => '4MC43556MS9479504',
                    'amount'         => 692.050000,
                    'payment_method' => 'paypal',
                    'type'           => 'payment',
                    'created_at'     => now(),
                    'updated_at'     => now()
                ],
                [
                    'sign'           => '+',
                    'order_id'       => 2,
                    'transaction_no' => 'txn_3OHh4LGAN8SIuxA70z0RaySc',
                    'amount'         => 720.000000,
                    'payment_method' => 'stripe',
                    'type'           => 'payment',
                    'created_at'     => now(),
                    'updated_at'     => now()
                ],
            ]);
        }
    }
}
