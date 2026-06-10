<?php

namespace Database\Seeders;


use App\Enums\Status;
use App\Models\Damage;
use App\Models\Product;
use App\Models\ProductVariation;
use App\Models\Stock;
use App\Services\ProductVariationService;
use Illuminate\Database\Seeder;
use Dipokhalder\EnvEditor\EnvEditor;


class DamageTableSeeder extends Seeder
{

    public int $i = 1, $j = 1;
    public array $damageTotals = [];

    public function run()
    {
        $damages  = [
            [
                'date'         => now(),
                'reference_no' => "1001"
            ],
            [
                'date'         => now(),
                'reference_no' => "1002"
            ],
            [
                'date'         => now(),
                'reference_no' => "1003"
            ]
        ];
        $envService = new EnvEditor();
        if ($envService->getValue('DEMO')) {
            foreach ($damages as $damage) {
                Damage::create([
                    'date'         => $damage['date'],
                    'reference_no' => $damage['reference_no'],
                    'tax'          => 0,
                    'discount'     => 0,
                    'subtotal'     => 0,
                    'total'        => 0,
                ]);
            }

            if ($envService->getValue('DISPLAY_TYPE') == 'fashion') {
                $productVariationService = new ProductVariationService();
                $products                = Product::inRandomOrder()->limit(3)->get()->pluck('id')->toArray();
                $productVariations       = ProductVariation::whereIn('product_id', $products)->where('parent_id', '!=', null)->get();

                if (count($productVariations) > 0) {
                    foreach ($productVariations as $productVariation) {
                        if (in_array($productVariation->product_id, $products)) {
                            $quantity = rand(1, 2);
                            $stock    = Stock::create([
                                'product_id'      => $productVariation->product_id,
                                'model_type'      => Damage::class,
                                'model_id'        => $this->j,
                                'item_type'       => ProductVariation::class,
                                'item_id'         => $productVariation->id,
                                'variation_names' => $productVariationService->ancestorsToString($productVariation),
                                'sku'             => $productVariation->sku,
                                'price'           => $productVariation->price,
                                'quantity'        => -$quantity,
                                'discount'        => 0,
                                'tax'             => 0,
                                'subtotal'        => $productVariation->price * $quantity,
                                'total'           => $productVariation->price * $quantity,
                                'status'          => Status::ACTIVE,
                            ]);

                            if (!isset($this->damageTotals[$this->j])) {
                                $this->damageTotals[$this->j] = $stock->total;
                            } else {
                                $this->damageTotals[$this->j] += $stock->total;
                            }

                            if ($this->i >= 6) {
                                $this->i = 0;
                                $this->j++;
                            }
                            $this->i++;
                        }
                    }

                    foreach ($this->damageTotals as $damageTotalKey => $damageTotal) {
                        $damage           = Damage::find($damageTotalKey);
                        $damage->total    = $damageTotal;
                        $damage->subtotal = $damageTotal;
                        $damage->save();
                    }
                }
            }
        }
    }
}
