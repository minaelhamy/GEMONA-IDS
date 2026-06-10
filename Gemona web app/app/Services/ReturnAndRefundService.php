<?php

namespace App\Services;

use Exception;
use App\Enums\Ask;
use App\Models\User;
use App\Models\Order;
use App\Models\ReturnAndRefund;
use App\Enums\ReturnOrderStatus;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use App\Http\Requests\PaginateRequest;
use App\Models\ReturnAndRefundProduct;
use Dipokhalder\Settings\Facades\Settings;
use App\Http\Requests\OrderStatusRequest;
use App\Http\Requests\ReturnAndRefundRequest;
use App\Libraries\QueryExceptionLibrary;

class ReturnAndRefundService
{
    public object $returnAndRefund;
    protected $returnFilter = [
        'order_serial_no',
        'user_id',
        'status',
    ];

    protected $exceptFilter = [
        'excepts'
    ];

    /**
     * @throws Exception
     */
    public function list(PaginateRequest $request, $auth = false)
    {
        try {
            $requests    = $request->all();
            $method      = $request->get('paginate', 0) == 1 ? 'paginate' : 'get';
            $methodValue = $request->get('paginate', 0) == 1 ? $request->get('per_page', 10) : '*';
            $orderColumn = $request->get('order_column') ?? 'id';
            $orderType   = $request->get('order_type') ?? 'desc';

            return ReturnAndRefund::where(function ($query) use ($requests, $auth) {
                if ($auth) {
                    $query->where('user_id', auth()->user()->id);
                }
                if (isset($requests['from_date']) && isset($requests['to_date'])) {
                    $first_date = Date('Y-m-d', strtotime($requests['from_date']));
                    $last_date  = Date('Y-m-d', strtotime($requests['to_date']));
                    $query->whereDate('created_at', '>=', $first_date)->whereDate(
                        'created_at',
                        '<=',
                        $last_date
                    );
                }
                foreach ($requests as $key => $request) {
                    if (in_array($key, $this->returnFilter)) {
                        $query->where($key, 'like', '%' . $request . '%');
                    }

                    if (in_array($key, $this->exceptFilter)) {
                        $explodes = explode('|', $request);
                        if (is_array($explodes)) {
                            foreach ($explodes as $explode) {
                                $query->where('id', '!=', $explode);
                            }
                        }
                    }
                }
            })->orderBy($orderColumn, $orderType)->$method(
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
    public function store(ReturnAndRefundRequest $request): object
    {
        try {
            $returnAndRefund = ReturnAndRefund::where('order_id', $request->order_id)->first();
            if ($returnAndRefund) {
                throw new Exception(trans('all.message.return_request_exist'), 422);
            } else {
                $order = Order::findOrFail($request->order_id);
                DB::transaction(function () use ($request, $order) {
                    $this->returnAndRefund = ReturnAndRefund::create([
                        'return_reason_id' => $request->return_reason_id,
                        'note'             => $request->note ? $request->note : "",
                        'order_id'         => $request->order_id,
                        'user_id'          => Auth::user()->id,
                        'order_serial_no'  => $request->order_serial_no,
                        'status'           => ReturnOrderStatus::PENDING
                    ]);

                    $orderDiscount = (float)$order->discount / (float)$order->subtotal;
                    if ($request->products) {
                        $products = json_decode($request->products, true);
                        foreach ($products as $product) {
                            $taxPrice       = (float)$product['tax'] / $product['order_quantity'];
                            $returnTaxPrice = $taxPrice * $product['quantity'];
                            $returnDiscount = $orderDiscount * $product['return_price'];
                            $returnPrice    = ($product['return_price'] - $returnDiscount) + $returnTaxPrice;

                            ReturnAndRefundProduct::create([
                                'return_and_refund_id' => $this->returnAndRefund->id,
                                'product_id'           => $product['product_id'],
                                'variation_id'         => $product['has_variation'] ? $product['variation_id'] : null,
                                'variation_names'      => $product['has_variation'] ? $product['variation_names'] : null,
                                'quantity'             => $product['quantity'],
                                'price'                => $product['price'],
                                'total'                => $product['total'],
                                'return_price'         => $returnPrice,
                                'user_id'              => Auth::user()->id,
                            ]);
                        }
                    }
                    if ($request->image) {
                        foreach ($request->image as $image) {
                            $this->returnAndRefund->addMedia($image)->toMediaCollection('return');
                        }
                    }
                });
                return $this->returnAndRefund;
            }
        } catch (Exception $exception) {
            DB::rollBack();
            Log::info($exception->getMessage());
            throw new Exception(QueryExceptionLibrary::message($exception), 422);
        }
    }

    /**
     * @throws Exception
     */
    public function show(ReturnAndRefund $returnAndRefund, $auth = false): ReturnAndRefund|array
    {
        try {
            if ($auth) {
                if ($returnAndRefund->user_id == Auth::user()->id) {
                    return $returnAndRefund;
                } else {
                    return [];
                }
            } else {
                return $returnAndRefund;
            }
        } catch (Exception $exception) {
            Log::info($exception->getMessage());
            throw new Exception(QueryExceptionLibrary::message($exception), 422);
        }
    }

    /**
     * @throws Exception
     */
    public function changeStatus(ReturnAndRefund $returnAndRefund, OrderStatusRequest $request): returnAndRefund
    {
        try {

            if ($request->status == ReturnOrderStatus::REJECTED) {
                $request->validate([
                    'reason' => 'required|max:700',
                ]);

                if ($request->reason) {
                    $returnAndRefund->reject_reason = $request->reason;
                }
            }

            $order = Order::findOrFail($returnAndRefund->order_id);
            if ($order->payment_method !== 1 && Settings::group('site')->get('site_is_return_product_price_add_to_credit') === Ask::YES) {
                $user          = User::findOrFail($returnAndRefund->user_id);
                $user->balance += $returnAndRefund?->returnProducts->sum('return_price');
                $user->save();
            }

            $returnAndRefund->status = $request->status;
            $returnAndRefund->save();
            return $returnAndRefund;
        } catch (Exception $exception) {
            Log::info($exception->getMessage());
            throw new Exception(QueryExceptionLibrary::message($exception), 422);
        }
    }
}
