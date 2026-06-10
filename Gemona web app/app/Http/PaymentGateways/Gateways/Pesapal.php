<?php

namespace App\Http\PaymentGateways\Gateways;

use Exception;
use GuzzleHttp\Client;
use App\Enums\Activity;
use App\Models\Currency;
use App\Enums\GatewayMode;
use App\Models\PaymentGateway;
use App\Services\PaymentService;
use App\Services\PaymentAbstract;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Dipokhalder\Settings\Facades\Settings;

class Pesapal extends PaymentAbstract
{
    protected string $baseUrl;
    protected string $consumerKey;
    protected string $consumerSecret;
    protected string $ipnId;
    protected Client $client;

    /**
     * @throws Exception
     */
    public function __construct()
    {
        $paymentService = new PaymentService();
        parent::__construct($paymentService);

        $this->paymentGateway = PaymentGateway::with('gatewayOptions')->where(['slug' => 'pesapal'])->first();
        if (!blank($this->paymentGateway)) {
            $this->paymentGatewayOption = $this->paymentGateway->gatewayOptions->pluck('value', 'option');

            $this->baseUrl = $this->paymentGatewayOption['pesapal_mode'] == GatewayMode::SANDBOX
                ? 'https://cybqa.pesapal.com/pesapalv3/api'
                : 'https://pay.pesapal.com/v3/api';

            $this->consumerKey = $this->paymentGatewayOption['pesapal_consumer_key'] ?? '';
            $this->consumerSecret = $this->paymentGatewayOption['pesapal_consumer_secret'] ?? '';
            $this->ipnId = $this->paymentGatewayOption['pesapal_ipn_id'] ?? '';
            $this->client = new Client();
        }
    }

    private function getAccessToken(): ?string
    {
        try {
            $response = $this->client->post("{$this->baseUrl}/Auth/RequestToken", [
                'json' => [
                    'consumer_key' => $this->consumerKey,
                    'consumer_secret' => $this->consumerSecret,
                ]
            ]);

            $data = json_decode($response->getBody(), true);
            return $data['token'] ?? null;
        } catch (Exception $e) {
            Log::error('Pesapal Auth Error: ' . $e->getMessage());
            return null;
        }
    }

    public function payment($order, $request)
    {
        try {
            $token = $this->getAccessToken();
            if ($token) {
                $currencyCode = 'KES';
                $currencyId = Settings::group('site')->get('site_default_currency');
                if (!blank($currencyId)) {
                    $currency = Currency::find($currencyId);
                    if ($currency) {
                        $currencyCode = $currency->code;
                    }
                }

                $payload = [
                    "id" => $order->order_serial_no,
                    "currency" => $currencyCode,
                    "amount" => number_format((float)$order->total, 2, '.', ''),
                    "description" => "Payment for order #" . $order->order_serial_no,
                    "callback_url" => route('payment.success', ['order' => $order, 'paymentGateway' => 'pesapal']),
                    "notification_id" => $this->ipnId,
                    "billing_address" => [
                        "email_address" => $order->user?->email,
                        "phone_number" => $order->user?->phone,
                        "first_name" => $order->user?->name,
                    ],
                ];

                $response = $this->client->post("{$this->baseUrl}/Transactions/SubmitOrderRequest", [
                    'headers' => [
                        'Authorization' => "Bearer {$token}",
                        'Accept' => 'application/json'
                    ],
                    'json' => $payload
                ]);

                $data = json_decode($response->getBody(), true);

                if (isset($data['redirect_url'])) {
                    return redirect($data['redirect_url']);
                }

                $message = $data['error']['message'] ?? trans('all.message.something_wrong');
                return redirect()->route('payment.index', [
                    'order' => $order,
                    'paymentGateway' => 'pesapal'
                ])->with('error', $message);
            } else {
                return redirect()->route('payment.index', [
                    'order' => $order,
                    'paymentGateway' => 'pesapal'
                ])->with('error', 'Unable to get Pesapal access token.');
            }
        } catch (Exception $e) {
            Log::error('Pesapal Payment Error: ' . $e->getMessage());
            return redirect()->route('payment.index', [
                'order' => $order,
                'paymentGateway' => 'pesapal'
            ])->with('error', $e->getMessage());
        }
    }


    public function status(): bool
    {
        $gateway = PaymentGateway::where(['slug' => 'pesapal', 'status' => Activity::ENABLE])->first();
        return (bool) $gateway;
    }

    public function success($order, $request)
    {
        try {
            $trackingId = $request->input('OrderTrackingId');
            $token = $this->getAccessToken();
            if ($token) {
                $response = $this->client->get("{$this->baseUrl}/Transactions/GetTransactionStatus", [
                    'headers' => [
                        'Authorization' => "Bearer {$token}",
                        'Accept' => 'application/json'
                    ],
                    'query' => [
                        'orderTrackingId' => $trackingId
                    ]
                ]);

                $data = json_decode($response->getBody(), true);

                if (($data['status_code'] ?? '') == 1) {
                    $this->paymentService->payment($order, 'pesapal', $trackingId);
                    return redirect()->route('payment.successful', ['order' => $order])
                        ->with('success', trans('all.message.payment_successful'));
                }

                return redirect()->route('payment.fail', [
                    'order' => $order,
                    'paymentGateway' => 'pesapal'
                ])->with('error', trans('all.message.transaction_failed'));
            } else {
                return redirect()->route('payment.fail', [
                    'order' => $order,
                    'paymentGateway' => 'pesapal'
                ])->with('error', 'Unable to get Pesapal access token.');
            }
        } catch (Exception $e) {
            Log::error('Pesapal Success Error: ' . $e->getMessage());
            DB::rollBack();
            return redirect()->route('payment.fail', [
                'order' => $order,
                'paymentGateway' => 'pesapal'
            ])->with('error', $e->getMessage());
        }
    }


    public function fail($order, $request)
    {
        return redirect()->route('payment.index', ['order' => $order])
            ->with('error', trans('all.message.something_wrong'));
    }

    public function cancel($order, $request)
    {
        return redirect()->route('home')->with('error', trans('all.message.payment_canceled'));
    }
}
