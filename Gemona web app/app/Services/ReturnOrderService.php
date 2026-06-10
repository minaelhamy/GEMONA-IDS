<?php

namespace App\Services;

use App\Models\Product;
use Exception;
use App\Http\Requests\ReturnOrderRequest;
use App\Http\Requests\PaginateRequest;
use App\Models\ReturnOrder;
use App\Models\Stock;
use App\Enums\Status;
use App\Libraries\QueryExceptionLibrary;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Models\ProductVariation;
use App\Models\StockTax;
use App\Models\Tax;


class ReturnOrderService
{

    public object $returnOrder;
    protected array $returnOrderFilter = [
        'user_id',
        'date',
        'reference_no',
        'total',
        'reason',
        'except'
    ];

    /**
     * @throws Exception
     */
    public function list(PaginateRequest $request)
    {
        try {
            $requests    = $request->all();
            $method      = $request->get('paginate', 0) == 1 ? 'paginate' : 'get';
            $methodValue = $request->get('paginate', 0) == 1 ? $request->get('per_page', 10) : '*';
            $orderColumn = $request->get('order_column') ?? 'id';
            $orderType   = $request->get('order_type') ?? 'desc';

            return ReturnOrder::with('user')->where(function ($query) use ($requests) {
                foreach ($requests as $key => $request) {
                    if (in_array($key, $this->returnOrderFilter)) {
                        if ($key == "except") {
                            $explodes = explode('|', $request);
                            if (count($explodes)) {
                                foreach ($explodes as $explode) {
                                    $query->where('id', '!=', $explode);
                                }
                            }
                        } else {
                            if ($key == "user_id") {
                                $query->where($key, $request);
                            } else if ($key == "date" && !empty($request)) {
                                $date_start = date('Y-m-d 00:00:00', strtotime($request));
                                $date_end   = date('Y-m-d 23:59:59', strtotime($request));
                                $query->where($key, '>=', $date_start)->where($key, '<=', $date_end);
                            } else {
                                $query->where($key, 'like', '%' . $request . '%');
                            }
                        }
                    }
                }
            })->orderBy($orderColumn, $orderType)->$method($methodValue);
        } catch (Exception $exception) {
            Log::info($exception->getMessage());
            throw new Exception(QueryExceptionLibrary::message($exception), 422);
        }
    }

    /**
     * @throws Exception
     */
    public function store(ReturnOrderRequest $request): object
    {
        try {
            DB::transaction(function () use ($request) {
                $this->returnOrder = ReturnOrder::create([
                    'user_id'      => $request->user_id,
                    'date'         => date('Y-m-d H:i:s', strtotime($request->date)),
                    'reference_no' => $request->reference_no,
                    'subtotal'     => $request->subtotal,
                    'tax'          => $request->tax,
                    'discount'     => $request->discount,
                    'total'        => $request->total,
                    'reason'       => $request->reason ? $request->reason : ""
                ]);

                if ($request->products) {
                    $model_id = $this->returnOrder->id;
                    $products = json_decode($request->products, true);
                    $taxes    = Tax::all()->keyBy('id');
                    foreach ($products as $product) {
                        $stock = Stock::create([
                            'model_type'      => ReturnOrder::class,
                            'model_id'        => $model_id,
                            'item_type'       => $product['is_variation'] ? ProductVariation::class : Product::class,
                            'item_id'         => $product['item_id'],
                            'variation_names' => $product['variation_names'],
                            'product_id'      => $product['product_id'],
                            'price'           => $product['price'],
                            'quantity'        => $product['quantity'],
                            'discount'        => $product['total_discount'],
                            'tax'             => $product['total_tax'],
                            'subtotal'        => $product['subtotal'],
                            'total'           => $product['total'],
                            'sku'             => $product['sku'],
                            'status'          => Status::ACTIVE
                        ]);

                        if (isset($product['tax_id']) && count($product['tax_id']) > 0) {
                            foreach ($product['tax_id'] as $tax_id) {
                                if (isset($taxes[$tax_id])) {
                                    $tax = $taxes[$tax_id];
                                    StockTax::create([
                                        'stock_id'   => $stock->id,
                                        'product_id' => $product['product_id'],
                                        'tax_id'     => $tax->id,
                                        'name'       => $tax->name,
                                        'code'       => $tax->code,
                                        'tax_rate'   => $tax->tax_rate,
                                        'tax_amount' => ($tax->tax_rate * ($product['price'] * $product['quantity'])) / 100,
                                    ]);
                                }
                            }
                        }
                    }
                }
                if ($request->file) {
                    $this->returnOrder->addMediaFromRequest('file')->toMediaCollection('returnOrder');
                }
            });
            return $this->returnOrder;
        } catch (Exception $exception) {
            Log::info($exception->getMessage());
            DB::rollBack();
            throw new Exception(QueryExceptionLibrary::message($exception), 422);
        }
    }

    /**
     * @throws Exception
     */
    public function show(ReturnOrder $returnOrder): ReturnOrder
    {
        try {
            return $returnOrder->load('media');
        } catch (Exception $exception) {
            Log::info($exception->getMessage());
            throw new Exception(QueryExceptionLibrary::message($exception), 422);
        }
    }

    /**
     * @throws Exception
     */
    public function edit(ReturnOrder $returnOrder): ReturnOrder
    {
        try {
            return $returnOrder;
        } catch (Exception $exception) {
            Log::info($exception->getMessage());
            throw new Exception(QueryExceptionLibrary::message($exception), 422);
        }
    }

    /**
     * @throws Exception
     */
    public function update(ReturnOrderRequest $request, ReturnOrder $returnOrder): object
    {
        try {
            DB::transaction(function () use ($request, $returnOrder) {
                $returnOrder->update([
                    'user_id'      => $request->user_id,
                    'date'         => date('Y-m-d H:i:s', strtotime($request->date)),
                    'reference_no' => $request->reference_no,
                    'subtotal'     => $request->subtotal,
                    'tax'          => $request->tax,
                    'discount'     => $request->discount,
                    'total'        => $request->total,
                    'reason'       => $request->reason ? $request->reason : "",
                ]);

                if ($request->products) {
                    $model_id = $returnOrder->id;
                    $products = json_decode($request->products, true);
                    if ($returnOrder->stocks) {
                        $stockIds = $returnOrder->stocks->pluck('id');
                        if (!blank($stockIds)) {
                            StockTax::whereIn('stock_id', $stockIds)->delete();
                        }
                        $returnOrder->stocks()->delete();
                    }
                    $taxes = Tax::all()->keyBy('id');
                    foreach ($products as $product) {
                        $stock = Stock::create([
                            'model_type'      => ReturnOrder::class,
                            'model_id'        => $model_id,
                            'item_type'       => $product['is_variation'] ? ProductVariation::class : Product::class,
                            'item_id'         => $product['item_id'],
                            'variation_names' => $product['variation_names'],
                            'product_id'      => $product['product_id'],
                            'price'           => $product['price'],
                            'quantity'        => $product['quantity'],
                            'discount'        => $product['total_discount'],
                            'tax'             => $product['total_tax'],
                            'subtotal'        => $product['subtotal'],
                            'total'           => $product['total'],
                            'sku'             => $product['sku'],
                            'status'          => Status::ACTIVE
                        ]);
                        if (isset($product['tax_id']) && count($product['tax_id']) > 0) {
                            foreach ($product['tax_id'] as $tax_id) {
                                if (isset($taxes[$tax_id])) {
                                    $tax = $taxes[$tax_id];
                                    StockTax::create([
                                        'stock_id'   => $stock->id,
                                        'product_id' => $product['product_id'],
                                        'tax_id'     => $tax->id,
                                        'name'       => $tax->name,
                                        'code'       => $tax->code,
                                        'tax_rate'   => $tax->tax_rate,
                                        'tax_amount' => ($tax->tax_rate * ($product['price'] * $product['quantity'])) / 100,
                                    ]);
                                }
                            }
                        }
                    }
                }
                if ($request->file) {
                    $file = $returnOrder->getFirstMedia('returnOrder');
                    if (isset($file)) {
                        $file->delete();
                    }
                    $returnOrder->addMediaFromRequest('file')->toMediaCollection('returnOrder');
                }
            });
            return $returnOrder;
        } catch (Exception $exception) {
            Log::info($exception->getMessage());
            DB::rollBack();
            throw new Exception(QueryExceptionLibrary::message($exception), 422);
        }
    }

    /**
     * @throws Exception
     */
    public function destroy(ReturnOrder $returnOrder): void
    {
        try {
            DB::transaction(function () use ($returnOrder) {
                if ($returnOrder->stocks) {
                    $stockIds = $returnOrder->stocks->pluck('id');
                    if (!blank($stockIds)) {
                        StockTax::whereIn('stock_id', $stockIds)->delete();
                    }
                    $returnOrder->stocks()->delete();
                }
                $file = $returnOrder->getFirstMedia('returnOrder');
                if ($file) {
                    $file->delete();
                }
                $returnOrder->delete();
            });
        } catch (Exception $exception) {
            Log::info($exception->getMessage());
            DB::rollBack();
            throw new Exception(QueryExceptionLibrary::message($exception), 422);
        }
    }

    public function downloadAttachment(ReturnOrder $returnOrder)
    {
        return $returnOrder->getMedia('returnOrder')->first();
    }
}
