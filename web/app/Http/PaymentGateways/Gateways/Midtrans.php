<?php

namespace App\Http\PaymentGateways\Gateways;


use App\Enums\Activity;
use App\Enums\GatewayMode;
use App\Models\Currency;
use GuzzleHttp\Client;
use App\Models\PaymentGateway;
use App\Services\PaymentAbstract;
use Exception;
use App\Services\PaymentService;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Dipokhalder\Settings\Facades\Settings;

class Midtrans extends PaymentAbstract
{
    public mixed $response;
    public string $baseUrl;
    public string $apiUrl;
    public object $client;

    public array $headers;

    public function __construct()
    {
        $paymentService = new PaymentService();
        parent::__construct($paymentService);
        $this->client = new Client();
        $this->paymentGateway = PaymentGateway::with('gatewayOptions')->where(['slug' => 'midtrans'])->first();
        $this->paymentGatewayOption = $this->paymentGateway->gatewayOptions->pluck('value', 'option');
        $this->baseUrl = $this->paymentGatewayOption['midtrans_mode'] == GatewayMode::SANDBOX ? "https://app.sandbox.midtrans.com/snap/v1/transactions" : "https://app.midtrans.com/snap/v1/transactions";
        $this->apiUrl = $this->paymentGatewayOption['midtrans_mode'] == GatewayMode::SANDBOX ? "https://api.sandbox.midtrans.com/v2" : "https://api.midtrans.com/v2";
        $this->headers = [
            'Accept' => 'application/json',
            'Content-Type' => 'application/json',
            'Authorization' => 'Basic ' . base64_encode($this->paymentGatewayOption['midtrans_server_key'] . ':'),
        ];
    }

    public function payment($order, $request): \Illuminate\Routing\Redirector|\Illuminate\Contracts\Foundation\Application|\Illuminate\Http\RedirectResponse
    {
        try {
            $currencyCode = 'IDR';
            $currencyId = Settings::group('site')->get('site_default_currency');
            if (!blank($currencyId)) {
                $currency = Currency::find($currencyId);
                if ($currency) {
                    $currencyCode = $currency->code;
                }
            }
            if (env('DEMO')) {
                $currencyCode = 'IDR';
            }

            if (!in_array($currencyCode, ["IDR", "SGD"])) {
                return redirect()->route('payment.index', [
                    'order'          => $order,
                    'paymentGateway' => 'midtrans'
                ])->with('error', "Currency is not supported . Use IDR or SGD");
            }

            $info = [
                "transaction_details" => [
                    "order_id"     => $order->order_serial_no,
                    "gross_amount" => round($order->total),
                    "currency"     => $currencyCode
                ],
                "callbacks" => [
                    "finish" => route('payment.success', [
                        'order'          => $order,
                        'paymentGateway' => 'midtrans'
                    ]),
                    "error" => route('payment.fail', [
                        'order'          => $order,
                        'paymentGateway' => 'midtrans'
                    ]),
                ],
                'customer_details' => [
                    'first_name' => $order->user?->FirstName,
                    'last_name'  => $order->user?->LastName,
                    'email'      => $order->user?->email,
                    'phone'      => $order->user?->phone,
                ],
            ];

            $response = $this->client->post($this->baseUrl, [
                'headers' => $this->headers,
                'json' => $info,
            ]);
            $resBody = json_decode($response->getBody(), true);
            if (isset($resBody['redirect_url'])) {
                return redirect($resBody['redirect_url']);
            } else {
                return redirect()->route('payment.fail', [
                    'order'          => $order,
                    'paymentGateway' => 'midtrans'
                ])->with('error', trans('all.message.something_wrong'));
            }
        } catch (Exception $e) {
            Log::info($e->getMessage());
            return redirect()->route('payment.index', [
                'order' => $order,
                'paymentGateway' => 'midtrans'
            ])->with('error', $e->getMessage());
        }
    }

    public function status(): bool
    {
        $paymentGateways = PaymentGateway::where(['slug' => 'midtrans', 'status' => Activity::ENABLE])->first();
        if ($paymentGateways) {
            return true;
        }
        return false;
    }

    public function success($order, $request): \Illuminate\Http\RedirectResponse
    {
        try {
            if ($request->query('transaction_status') == "capture" || $request->query('transaction_status') == "settlement") {
                $trans_id = $order->order_serial_no;

                $res = $this->client->get($this->apiUrl . '/' . $order->order_serial_no . '/status', [
                    'headers' => $this->headers,
                ]);
                $resBody = json_decode($res->getBody(), true);
                if (isset($resBody['transaction_id'])) {
                    $trans_id = $resBody['transaction_id'];
                }
                $paymentService = new PaymentService;
                $paymentService->payment($order, 'midtrans', $trans_id);
                return redirect()->route('payment.successful', ['order' => $order])->with('success', trans('all.message.payment_successful'));
            } else {
                return redirect()->route('payment.fail', [
                    'order' => $order,
                    'paymentGateway' => 'midtrans'
                ])->with('error', $this->response['message'] ?? trans('all.message.something_wrong'));
            }
        } catch (Exception $e) {
            Log::info($e->getMessage());
            DB::rollBack();
            return redirect()->route('payment.fail', [
                'order' => $order,
                'paymentGateway' => 'midtrans'
            ])->with('error', $e->getMessage());
        }
    }

    public function fail($order, $request): \Illuminate\Http\RedirectResponse
    {
        return redirect()->route('payment.index', ['order' => $order, 'paymentGateway' => 'midtrans'])->with('error', trans('all.message.something_wrong'));
    }

    public function cancel($order, $request): \Illuminate\Http\RedirectResponse
    {
        return redirect('/checkout/payment');
    }
}
