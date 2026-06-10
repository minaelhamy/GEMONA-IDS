<?php

namespace App\Services;

use Exception;
use App\Enums\Status;
use App\Models\Order;
use App\Models\Stock;
use App\Models\Outlet;
use App\Models\Address;
use App\Models\Product;
use App\Enums\OrderType;
use App\Models\StockTax;
use App\Enums\AddressType;
use App\Enums\OrderStatus;
use App\Models\OrderCoupon;
use App\Enums\PaymentStatus;
use App\Events\SendOrderSms;
use App\Models\OrderAddress;
use App\Events\SendOrderMail;
use App\Events\SendOrderPush;
use App\Models\ProductVariation;
use App\Models\OrderOutletAddress;
use Illuminate\Support\Facades\DB;
use App\Http\Requests\OrderRequest;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use App\Http\Requests\PaginateRequest;
use App\Http\Requests\OrderStatusRequest;
use App\Libraries\QueryExceptionLibrary;

class FrontendOrderService
{

    public object $order;
    protected array $frontendOrderFilter = [
        'order_serial_no',
        'user_id',
        'total',
        'order_type',
        'order_datetime',
        'payment_method',
        'payment_status',
        'status',
        'active'
    ];

    protected array $exceptFilter = [
        'excepts'
    ];

    /**
     * @throws Exception
     */
    public function myOrder(PaginateRequest $request)
    {
        try {
            $requests            = $request->all();
            $method              = $request->get('paginate', 0) == 1 ? 'paginate' : 'get';
            $methodValue         = $request->get('paginate', 0) == 1 ? $request->get('per_page', 10) : '*';
            $frontendOrderColumn = $request->get('order_column') ?? 'id';
            $frontendOrderType   = $request->get('order_by') ?? 'desc';

            return Order::where('order_type', "!=", OrderType::POS)->where(function ($query) use ($requests) {
                $query->where('user_id', auth()->user()->id);
                foreach ($requests as $key => $request) {
                    if (in_array($key, $this->frontendOrderFilter)) {
                        if ($key === "status") {
                            $query->where($key, (int)$request);
                        } else {
                            $query->where($key, 'like', '%' . $request . '%');
                        }
                    }
                    if (in_array($key, $this->exceptFilter)) {
                        $explodes = explode('|', $request);
                        if (is_array($explodes)) {
                            foreach ($explodes as $explode) {
                                $query->where('status', '!=', $explode);
                            }
                        }
                    }
                }
            })->orderBy($frontendOrderColumn, $frontendOrderType)->$method(
                $methodValue
            );
        } catch (Exception $exception) {
            Log::info($exception->getMessage());
            throw new Exception(QueryExceptionLibrary::message($exception), 422);
        }
    }

    /**
     * @throws Exception
     */
    public function myOrderStore(OrderRequest $request): object
    {
        try {
            DB::transaction(function () use ($request) {
                $oldOrder     = Order::where(['user_id' => Auth::user()->id, 'active' => Status::INACTIVE]);
                $orderReplace = $oldOrder;
                if (!blank($oldOrder->get())) {
                    $ids          = $oldOrder->pluck('id');
                    $stock        = Stock::whereIn('model_id', $ids)->where(['model_type' => Order::class, 'status' => Status::INACTIVE]);
                    $stockReplace = $stock;
                    $stock        = $stock->get();
                    $stockIds     = $stock->pluck('id');
                    if (!blank($stockIds)) {
                        StockTax::whereIn('stock_id', $stockIds)?->delete();
                    }
                    $stockReplace?->delete();
                    OrderAddress::whereIn('order_id', $ids)->where(['user_id' => Auth::user()->id])?->delete();
                    OrderOutletAddress::whereIn('order_id', $ids)->where(['user_id' => Auth::user()->id])?->delete();
                    OrderCoupon::whereIn('order_id', $ids)->where(['user_id' => Auth::user()->id])?->delete();
                    $orderReplace->delete();
                }

                $this->order = Order::create(
                    $request->validated() + [
                        'user_id'        => Auth::user()->id,
                        'status'         => OrderStatus::PENDING,
                        'payment_status' => PaymentStatus::UNPAID,
                        'order_datetime' => date('Y-m-d H:i:s')
                    ]
                );

                $products = json_decode($request->products);
                if (!blank($products)) {
                    foreach ($products as $product) {
                        $stockId = Stock::create([
                            'product_id'      => $product->product_id,
                            'model_type'      => Order::class,
                            'model_id'        => $this->order->id,
                            'item_type'       => $product->variation_id > 0 ? ProductVariation::class : Product::class,
                            'item_id'         => $product->variation_id > 0 ? $product->variation_id : $product->product_id,
                            'variation_names' => $product->variation_names,
                            'sku'             => $product->sku,
                            'price'           => $product->price,
                            'quantity'        => -$product->quantity,
                            'discount'        => $product->discount,
                            'tax'             => number_format($product->total_tax, env('CURRENCY_DECIMAL_POINT'), '.', ''),
                            'subtotal'        => $product->subtotal,
                            'total'           => $product->total,
                            'status'          => Status::INACTIVE,
                        ]);
                        if ($product->taxes) {
                            $j               = 0;
                            $productTaxArray = [];
                            foreach ($product->taxes as $tax) {
                                $productTaxArray[$j] = [
                                    'stock_id'   => $stockId->id,
                                    'product_id' => $product->product_id,
                                    'tax_id'     => $tax->id,
                                    'name'       => $tax->name,
                                    'code'       => $tax->code,
                                    'tax_rate'   => $tax->tax_rate,
                                    'tax_amount' => $tax->tax_amount,
                                    'created_at' => now(),
                                    'updated_at' => now()
                                ];
                                $j++;
                            }
                            StockTax::insert($productTaxArray);
                        }
                    }
                }

                $this->order->order_serial_no = date('dmy') . $this->order->id;
                $this->order->save();

                if ($request->order_type == OrderType::DELIVERY) {
                    $shippingAddress = Address::find($request->shipping_id);
                    $billingAddress  = Address::find($request->billing_id);
                    if ($shippingAddress) {
                        OrderAddress::create([
                            'order_id'     => $this->order->id,
                            'user_id'      => $shippingAddress->user_id,
                            'address_type' => AddressType::SHIPPING,
                            'full_name'    => $shippingAddress->full_name,
                            'email'        => $shippingAddress->email,
                            'country_code' => $shippingAddress->country_code,
                            'phone'        => $shippingAddress->phone,
                            'country'      => $shippingAddress->country,
                            'address'      => $shippingAddress->address,
                            'state'        => $shippingAddress->state,
                            'city'         => $shippingAddress->city,
                            'zip_code'     => $shippingAddress->zip_code,
                            'latitude'     => $shippingAddress->latitude,
                            'longitude'    => $shippingAddress->longitude,
                        ]);
                    }
                    if ($billingAddress) {
                        OrderAddress::create([
                            'order_id'     => $this->order->id,
                            'user_id'      => $shippingAddress->user_id,
                            'address_type' => AddressType::BILLING,
                            'full_name'    => $billingAddress->full_name,
                            'email'        => $billingAddress->email,
                            'country_code' => $billingAddress->country_code,
                            'phone'        => $billingAddress->phone,
                            'country'      => $billingAddress->country,
                            'address'      => $billingAddress->address,
                            'state'        => $billingAddress->state,
                            'city'         => $billingAddress->city,
                            'zip_code'     => $billingAddress->zip_code,
                            'latitude'     => $billingAddress->latitude,
                            'longitude'    => $billingAddress->longitude,
                        ]);
                    }
                } elseif ($request->order_type === OrderType::PICK_UP) {
                    $outletAddress = Outlet::find($request->outlet_id);
                    if ($outletAddress) {
                        OrderOutletAddress::create([
                            'order_id'     => $this->order->id,
                            'user_id'      => $this->order->user_id,
                            'name'         => $outletAddress->name,
                            'email'        => $outletAddress->email,
                            'phone'        => $outletAddress->phone,
                            'country_code' => $outletAddress->country_code,
                            'latitude'     => $outletAddress->latitude,
                            'longitude'    => $outletAddress->longitude,
                            'city'         => $outletAddress->city,
                            'state'        => $outletAddress->state,
                            'zip_code'     => $outletAddress->zip_code,
                            'address'      => $outletAddress->address,
                        ]);
                    }
                }

                if ($request->coupon_id > 0) {
                    OrderCoupon::create([
                        'order_id'  => $this->order->id,
                        'coupon_id' => $request->coupon_id,
                        'user_id'   => Auth::user()->id,
                        'discount'  => $request->discount
                    ]);
                }
            });
            return $this->order;
        } catch (Exception $exception) {
            DB::rollBack();
            Log::info($exception->getMessage());
            throw new Exception(QueryExceptionLibrary::message($exception), 422);
        }
    }

    /**
     * @throws Exception
     */
    public function show(Order $order): Order|array
    {
        try {
            if ($order->user_id == Auth::user()->id) {
                return $order;
            }
            return [];
        } catch (Exception $exception) {
            Log::info($exception->getMessage());
            throw new Exception(QueryExceptionLibrary::message($exception), 422);
        }
    }

    /**
     * @throws Exception
     */
    public function changeStatus(Order $order, OrderStatusRequest $request): Order
    {
        try {
            if ($order->user_id == Auth::user()->id) {
                if ($request->status == OrderStatus::CANCELED) {
                    if ($order->status >= OrderStatus::CONFIRMED) {
                        throw new Exception(trans('all.message.order_confirmed'), 422);
                    } else {
                        if ($order->transaction) {
                            app(PaymentService::class)->cashBack(
                                $order,
                                'credit',
                                rand(111111111111111, 99999999999999)
                            );
                        }
                        SendOrderMail::dispatch(['order_id' => $order->id, 'status' => $request->status]);
                        SendOrderSms::dispatch(['order_id' => $order->id, 'status' => $request->status]);
                        SendOrderPush::dispatch(['order_id' => $order->id, 'status' => $request->status]);

                        $stocks = Stock::where(['model_type' => Order::class, 'model_id' => $order->id])->get();
                        foreach ($stocks as $stock) {
                            $stock->status = Status::INACTIVE;
                            $stock->save();
                        };

                        $order->status = $request->status;
                        $order->save();
                    }
                }
            }
            return $order;
        } catch (Exception $exception) {
            Log::info($exception->getMessage());
            throw new Exception(QueryExceptionLibrary::message($exception), 422);
        }
    }
}
