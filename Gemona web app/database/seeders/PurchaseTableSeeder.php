<?php

namespace Database\Seeders;

use App\Enums\Ask;
use App\Enums\Status;
use App\Models\Product;
use App\Models\ProductVariation;
use App\Models\Purchase;
use App\Enums\PurchaseStatus;
use App\Models\Stock;
use App\Services\ProductVariationService;
use Illuminate\Database\Seeder;
use Dipokhalder\EnvEditor\EnvEditor;


class PurchaseTableSeeder extends Seeder
{

    public int $i = 1, $j = 1;
    public array $purchaseSubtotals = [];
    public array $purchaseTotals = [];

    public function run()
    {
        $purchases  = [
            [
                'supplier'     => 1,
                'date'         => now(),
                'reference_no' => "1001",
                'status'       => PurchaseStatus::RECEIVED,
                'total'        => 0,
            ],
            [
                'supplier'     => 2,
                'date'         => now(),
                'reference_no' => "1002",
                'status'       => PurchaseStatus::RECEIVED,
                'total'        => 0,
            ],
            [
                'supplier'     => 1,
                'date'         => now(),
                'reference_no' => "1003",
                'status'       => PurchaseStatus::RECEIVED,
                'total'        => 0,
            ],
            [
                'supplier'     => 1,
                'date'         => now(),
                'reference_no' => "1004",
                'status'       => PurchaseStatus::RECEIVED,
                'total'        => 0,
            ],
            [
                'supplier'     => 2,
                'date'         => now(),
                'reference_no' => "1005",
                'status'       => PurchaseStatus::RECEIVED,
                'total'        => 0,
            ],
            [
                'supplier'     => 1,
                'date'         => now(),
                'reference_no' => "1006",
                'status'       => PurchaseStatus::RECEIVED,
                'total'        => 0,
            ],
            [
                'supplier'     => 1,
                'date'         => now(),
                'reference_no' => "1007",
                'status'       => PurchaseStatus::RECEIVED,
                'total'        => 0,
            ],
            [
                'supplier'     => 2,
                'date'         => now(),
                'reference_no' => "1008",
                'status'       => PurchaseStatus::RECEIVED,
                'total'        => 0,
            ],
            [
                'supplier'     => 2,
                'date'         => now(),
                'reference_no' => "1009",
                'status'       => PurchaseStatus::RECEIVED,
                'total'        => 0,
            ],
            [
                'supplier'     => 1,
                'date'         => now(),
                'reference_no' => "1010",
                'status'       => PurchaseStatus::RECEIVED,
                'total'        => 0,
            ],
            [
                'supplier'     => 1,
                'date'         => now(),
                'reference_no' => "1011",
                'status'       => PurchaseStatus::RECEIVED,
                'total'        => 0,
            ],
            [
                'supplier'     => 2,
                'date'         => now(),
                'reference_no' => "1012",
                'status'       => PurchaseStatus::RECEIVED,
                'total'        => 0,
            ],
            [
                'supplier'     => 1,
                'date'         => now(),
                'reference_no' => "1013",
                'status'       => PurchaseStatus::RECEIVED,
                'total'        => 0,
            ],
        ];
        $envService = new EnvEditor();
        if ($envService->getValue('DEMO')) {
            foreach ($purchases as $purchase) {
                Purchase::create([
                    'supplier_id'  => $purchase['supplier'],
                    'date'         => $purchase['date'],
                    'reference_no' => $purchase['reference_no'],
                    'status'       => $purchase['status'],
                    'tax'          => 0,
                    'discount'     => 0,
                    'subtotal'     => 0,
                    'total'        => $purchase['total'],
                ]);
            }

            if ($envService->getValue('DISPLAY_TYPE') == 'fashion') {
                $productVariationService = new ProductVariationService();
                $products                = Product::select('id', 'can_purchasable')->where('can_purchasable', Ask::NO)->get()->pluck('id')->toArray();
                $productVariations       = ProductVariation::where('parent_id', '!=', null)->get();

                if (count($productVariations) > 0) {
                    foreach ($productVariations as $productVariation) {
                        if (!in_array($productVariation->product_id, $products)) {
                            $quantity = rand(10, 100);
                            $stock    = Stock::create([
                                'product_id'      => $productVariation->product_id,
                                'model_type'      => Purchase::class,
                                'model_id'        => $this->j,
                                'item_type'       => ProductVariation::class,
                                'item_id'         => $productVariation->id,
                                'variation_names' => $productVariationService->ancestorsToString($productVariation),
                                'sku'             => $productVariation->sku,
                                'price'           => $productVariation->price - 20,
                                'quantity'        => $quantity,
                                'discount'        => 0,
                                'tax'             => 0,
                                'subtotal'        => (($productVariation->price - 20) * $quantity),
                                'total'           => (($productVariation->price - 20) * $quantity),
                                'status'          => Status::ACTIVE,
                            ]);

                            if (!isset($this->purchaseTotals[$this->j])) {
                                $this->purchaseTotals[$this->j] = $stock->total;
                            } else {
                                $this->purchaseTotals[$this->j] += $stock->total;
                            }

                            if ($this->i >= 50) {
                                $this->i = 0;
                                $this->j++;
                            }
                            $this->i++;
                        }
                    }

                    foreach ($this->purchaseTotals as $purchaseTotalKey => $purchaseTotal) {
                        $purchase           = Purchase::find($purchaseTotalKey);
                        $purchase->total    = $purchaseTotal;
                        $purchase->subtotal = $purchaseTotal;
                        $purchase->save();
                    }
                }
            }
        }
    }
}
