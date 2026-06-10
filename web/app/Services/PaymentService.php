<?php

namespace App\Services;

use Exception;
use App\Enums\Ask;
use App\Models\User;
use App\Enums\Status;
use App\Models\Order;
use App\Models\Stock;
use App\Enums\OrderStatus;
use App\Models\Transaction;
use App\Enums\PaymentStatus;
use App\Events\SendOrderSms;
use App\Events\SendOrderMail;
use App\Events\SendOrderPush;
use App\Events\SendOrderGotSms;
use App\Events\SendOrderGotMail;
use App\Events\SendOrderGotPush;
use App\Libraries\QueryExceptionLibrary;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class PaymentService
{

    public object $transaction;

    /**
     * @throws Exception
     */
    public function payment($order, $gatewaySlug, $transactionNo): object
    {
        try {
            DB::transaction(function () use ($order, $gatewaySlug, $transactionNo) {
                $transaction = Transaction::where(['order_id' => $order->id])->first();
                if (!$transaction) {
                    $transaction = Transaction::create([
                        'order_id'       => $order->id,
                        'transaction_no' => $transactionNo,
                        'amount'         => $order->total,
                        'payment_method' => $gatewaySlug,
                        'sign'           => '+',
                        'type'           => 'payment'
                    ]);
                }
                $this->transaction     = $transaction;
                $order->active         = Ask::YES;
                $order->payment_status = PaymentStatus::PAID;
                $order->save();
                Stock::where(['model_id' => $order->id, 'model_type' => Order::class, 'status' => Status::INACTIVE])?->update(['status' => Status::ACTIVE]);

                SendOrderMail::dispatch(['order_id' => $order->id, 'status' => OrderStatus::PENDING]);
                SendOrderSms::dispatch(['order_id' => $order->id, 'status' => OrderStatus::PENDING]);
                SendOrderPush::dispatch(['order_id' => $order->id, 'status' => OrderStatus::PENDING]);

                SendOrderGotMail::dispatch(['order_id' => $order->id]);
                SendOrderGotSms::dispatch(['order_id' => $order->id]);
                SendOrderGotPush::dispatch(['order_id' => $order->id]);
            });
            return $this->transaction;
        } catch (Exception $exception) {
            Log::info($exception->getMessage());
            DB::rollBack();
            throw new Exception(QueryExceptionLibrary::message($exception), 422);
        }
    }

    public function cashBack($order, $gatewaySlug, $transactionNo)
    {
        $transaction = Transaction::where(['order_id' => $order->id])->first();
        if ($transaction) {
            $transaction = Transaction::create([
                'order_id'       => $order->id,
                'transaction_no' => $transactionNo,
                'amount'         => $order->total,
                'payment_method' => $gatewaySlug,
                'sign'           => '-',
                'type'           => 'cash_back'
            ]);

            $user = User::find($order->user_id);
            if ($user) {
                $user->balance = ($user->balance + $order->total);
                $user->save();
            }
        }

        return $transaction;
    }
}
