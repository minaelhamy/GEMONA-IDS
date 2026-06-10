<?php

namespace App\Http\PaymentGateways\Gateways;


use Exception;
use App\Enums\Activity;
use App\Enums\GatewayMode;
use App\Models\PaymentGateway;
use App\Services\PaymentService;
use App\Services\PaymentAbstract;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Http;

class Phonepe extends PaymentAbstract
{
    public mixed $response;

    public function __construct()
    {
        $paymentService = new PaymentService();
        parent::__construct($paymentService);
        $this->paymentGateway       = PaymentGateway::with('gatewayOptions')->where(['slug' => 'phonepe'])->first();
        $this->paymentGatewayOption = $this->paymentGateway->gatewayOptions->pluck('value', 'option');
        Config::set('phonepe.merchantId', $this->paymentGatewayOption['phonepe_client_id']);
        Config::set('phonepe.merchantUserId', $this->paymentGatewayOption['phonepe_merchant_user_id']);
        Config::set('phonepe.env', $this->paymentGatewayOption['phonepe_mode'] == GatewayMode::SANDBOX ? 'https://api-preprod.phonepe.com/apis/pg-sandbox' : 'https://api.phonepe.com/apis/pg');
        Config::set('phonepe.authEnv', $this->paymentGatewayOption['phonepe_mode'] == GatewayMode::SANDBOX ? 'https://api-preprod.phonepe.com/apis/pg-sandbox' : 'https://api.phonepe.com/apis/identity-manager');
        Config::set('phonepe.saltIndex', $this->paymentGatewayOption['phonepe_key_index']);
        Config::set('phonepe.saltKey', $this->paymentGatewayOption['phonepe_secret_key']);
    }

    public function payment($order, $request)
    {
        try {

            Config::set('phonepe.redirectUrl', route('payment.success', ['order' => $order, 'paymentGateway' => 'phonepe']));
            Config::set('phonepe.callBackUrl', route('payment.success', ['order' => $order, 'paymentGateway' => 'phonepe']));

            $merchantId = Config::get('phonepe.merchantId');
            $saltIndex = Config::get('phonepe.saltIndex');
            $saltKey = Config::get('phonepe.saltKey');
            $paymentEnv = Config::get('phonepe.env');
            $authEnv = Config::get('phonepe.authEnv');

            $response = $this->authorizePhonepe($authEnv, $merchantId, $saltIndex, $saltKey);
            if ($response->ok()) {
                $bearerToken = $response->json()['access_token'];
                //make payment
                $makePayment = Http::withHeaders([
                    'Content-Type' => 'application/json',
                    'Authorization' => 'O-Bearer ' . $bearerToken . ''
                ])->post($paymentEnv . '/checkout/v2/pay', [
                    'merchantOrderId'      => $order->order_serial_no,
                    'amount'               => round($order->total * 100),
                    'paymentFlow'          => [
                        'type'       => 'PG_CHECKOUT',
                        'message'    => 'Payment message used for collect requests.',
                        'merchantUrls'  => [
                            'redirectUrl'   => route('payment.success', ['order' => $order, 'paymentGateway' => 'phonepe'])
                        ]
                    ]
                ]);
                if ($makePayment->ok() && array_key_exists("redirectUrl", $makePayment->json())) {
                    return redirect()->away($makePayment->json()['redirectUrl']);
                }
            } else {
                return redirect()->route('payment.index', [
                    'order'          => $order,
                    'paymentGateway' => 'phonepe'
                ])->with('error', "JSON Data parsing error!");
            }
        } catch (Exception $e) {
            Log::info($e->getMessage());
            return redirect()->route('payment.index', [
                'order'          => $order,
                'paymentGateway' => 'phonepe'
            ])->with('error', $e->getMessage());
        }
    }

    private function authorizePhonepe($authEnv, $merchantId, $saltIndex, $saltKey)
    {
        $response = Http::withHeaders([
            'Content-Type' => 'application/x-www-form-urlencoded',
        ])->asForm()->post($authEnv . '/v1/oauth/token', [
            'client_id'     => $merchantId,
            'client_version' => $saltIndex,
            'client_secret' => $saltKey,
            'grant_type'    => 'client_credentials',
        ]);

        return $response;
    }

    private function verifyPaymentStatus($merchantOrderId): array
    {
        try {
            $authEnv = Config::get('phonepe.authEnv');
            $merchantId = Config::get('phonepe.merchantId');
            $saltKey = Config::get('phonepe.saltKey');
            $saltIndex = Config::get('phonepe.saltIndex');
            $paymentEnv = Config::get('phonepe.env');


            $authResponse = $this->authorizePhonepe($authEnv, $merchantId, $saltIndex, $saltKey);

            if (!$authResponse->ok()) {
                Log::error('PhonePe auth failed:', $authResponse->json());
                return ['state' => 'FAILED', 'message' => 'Auth token error'];
            }

            $token = $authResponse->json()['access_token'];

            $response = Http::withHeaders([
                'Content-Type' => 'application/json',
                'Authorization' => 'O-Bearer ' . $token
            ])->get($paymentEnv . '/checkout/v2/order/' . $merchantOrderId . '/status');

            if (!$response->ok()) {
                Log::error('PhonePe status check failed:', $response->json());
                return ['state' => 'FAILED', 'message' => 'Payment check failed'];
            }

            return $response->json();
        } catch (\Exception $e) {
            Log::error('PhonePe status check error: ' . $e->getMessage());
            return ['state' => 'FAILED', 'message' => $e->getMessage()];
        }
    }


    public function status(): bool
    {
        $paymentGateways = PaymentGateway::where(['slug' => 'phonepe', 'status' => Activity::ENABLE])->first();
        if ($paymentGateways) {
            return true;
        }
        return false;
    }

    public function success($order, $request): \Illuminate\Http\RedirectResponse
    {
        $verifyPayment = $this->verifyPaymentStatus($order->order_serial_no);
        try {
            if (isset($verifyPayment['orderId']) && $verifyPayment['state'] == "COMPLETED") {
                $paymentService = new PaymentService;
                $paymentService->payment($order, 'phonepe', $verifyPayment['orderId']);
                return redirect()->route('payment.successful', ['order' => $order])->with('success', trans('all.message.payment_successful'));
            } else {
                return redirect()->route('payment.fail', [
                    'order'          => $order,
                    'paymentGateway' => 'phonepe'
                ])->with('error', $this->response['message'] ?? trans('all.message.something_wrong'));
            }
        } catch (Exception $e) {
            Log::info($e->getMessage());
            DB::rollBack();
            return redirect()->route('payment.fail', [
                'order'          => $order,
                'paymentGateway' => 'phonepe'
            ])->with('error', $e->getMessage());
        }
    }

    public function fail($order, $request): \Illuminate\Http\RedirectResponse
    {
        return redirect()->route('payment.index', ['paymentGateway' => 'phonepe', 'order' => $order])->with('error', trans('all.message.something_wrong'));
    }

    public function cancel($order, $request): \Illuminate\Http\RedirectResponse
    {
        return redirect()->route('home')->with('error', trans('all.message.payment_canceled'));
    }
}
