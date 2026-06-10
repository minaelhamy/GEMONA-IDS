<?php

namespace App\Http\PaymentGateways\Gateways;

use Exception;
use App\Enums\Activity;
use App\Enums\GatewayMode;
use App\Models\PaymentGateway;
use App\Services\PaymentService;
use App\Services\PaymentAbstract;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Log;
use Zfhassaan\Easypaisa\Easypaisa as EasypaisaClient;

class Easypaisa extends PaymentAbstract
{
    protected string $storeId;
    protected string $apiUrl;
    protected string $hashKey;
    protected string $mode;
    protected EasypaisaClient $client;

    public function __construct()
    {
        $paymentService = new PaymentService();
        parent::__construct($paymentService);
        $this->paymentGateway = PaymentGateway::with('gatewayOptions')->where(['slug' => 'easypaisa'])->first();
        if (!blank($this->paymentGateway)) {
            $this->paymentGatewayOption = $this->paymentGateway->gatewayOptions->pluck('value', 'option');
            $this->apiUrl               = 'https://easypaystg.easypaisa.com.pk/tpg/';
            $this->storeId              = $this->paymentGatewayOption['easypaisa_store_id'];
            $this->hashKey              = $this->paymentGatewayOption['easypaisa_hash_key'];
            $this->mode                 = $this->paymentGatewayOption['easypaisa_mode'] == GatewayMode::SANDBOX ? 'Sandbox' : 'Production';

            Config::set("easypaisa.mode", $this->mode);
            Config::set("easypaisa.type", 'Hosted');
            Config::set("easypaisa.callback", 'http://foodking.lrvl/payment/success/easypaisa');
            Config::set("easypaisa.hosted", '');

            Config::set("easypaisa.sandbox_url", $this->apiUrl);
            Config::set("easypaisa.sandbox_username", $this->paymentGatewayOption['easypaisa_username']);
            Config::set("easypaisa.sandbox_password", $this->paymentGatewayOption['easypaisa_password']);
            Config::set("easypaisa.sandbox_storeid", $this->storeId);
            Config::set("easypaisa.sandbox_hashkey", $this->hashKey);

            Config::set("easypaisa.prod_url", $this->apiUrl);
            Config::set("easypaisa.prod_username", $this->paymentGatewayOption['easypaisa_username']);
            Config::set("easypaisa.prod_password", $this->paymentGatewayOption['easypaisa_password']);
            Config::set("easypaisa.prod_storeid", $this->storeId);
            Config::set("easypaisa.prod_hashkey", $this->hashKey);


            $this->client = new EasypaisaClient([
                'storeId'     => $this->storeId,
                'hashKey'     => $this->hashKey,
                'api_mode'    => $this->mode,
            ]);
        }
    }

    public function payment($order, $request) : \Illuminate\Routing\Redirector|\Illuminate\Contracts\Foundation\Application|\Illuminate\Http\RedirectResponse
    {
        try {
            $orderRefNum = $order->order_serial_no;
            $amount = number_format($order->total * 100);
            $checkoutUrl = $this->client->sendHostedRequest([
                'amount'         => strip_tags($amount),
                'orderRefNum'    => strip_tags($orderRefNum),
                'paymentMethod'  => 'InitialRequest',
                'postBackURL'    => route('payment.success', ['order' => $order, 'paymentGateway' => 'easypaisa']),
                'storeId'        => $this->storeId,
                'timeStamp'      => now()->format('YmdHis'), 
                'mobileAccountNo' => '',
            ]);
            return redirect()->away($checkoutUrl);
        } catch (Exception $e) {
            Log::info('Easypaisa Payment Error: ' . $e->getMessage());
            return redirect()->route('payment.index', [
                'order'          => $order,
                'paymentGateway' => 'easypaisa'
            ])->with('error', trans('all.message.something_wrong'));
        }
    }

    public function status(): bool
    {
        $paymentGateways = PaymentGateway::where(['slug' => 'easypaisa', 'status' => Activity::ENABLE])->first();
        return (bool) $paymentGateways;
    }

    public function success($order, $request) : \Illuminate\Http\RedirectResponse
    {
        try {
            if (isset($request['responseCode']) && $request['responseCode'] == '000') {
                $this->paymentService->payment($order, 'easypaisa', $request['orderRefNum']);
                return redirect()->route('payment.successful', ['order' => $order])
                    ->with('success', trans('all.message.payment_successful'));
            } else {
                return redirect()->route('payment.fail', [
                    'order'          => $order,
                    'paymentGateway' => 'easypaisa'
                ])->with('error', trans('all.message.something_wrong'));
            }
        } catch (Exception $e) {
            Log::info('Easypaisa Success Error: ' . $e->getMessage());
            return redirect()->route('payment.fail', [
                'order'          => $order,
                'paymentGateway' => 'easypaisa'
            ])->with('error', $e->getMessage());
        }
    }

    public function fail($order, $request) : \Illuminate\Http\RedirectResponse
    {
        return redirect()->route('payment.index', ['order' => $order])
            ->with('error', trans('all.message.something_wrong'));
    }

    public function cancel($order, $request) : \Illuminate\Http\RedirectResponse
    {
        return redirect('/checkout/payment')->with('error', trans('all.message.payment_canceled'));
    }
}
