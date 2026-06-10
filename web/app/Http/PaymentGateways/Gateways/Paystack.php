<?php

namespace App\Http\PaymentGateways\Gateways;

use Exception;
use GuzzleHttp\Client;
use App\Enums\Activity;
use App\Models\Currency;
use App\Models\PaymentGateway;
use App\Services\PaymentService;
use App\Services\PaymentAbstract;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Dipokhalder\Settings\Facades\Settings;

class Paystack extends PaymentAbstract
{
    protected string $publicKey;
    protected string $secretKey;
    protected string $paymentUrl;
    protected Client $client;

    /**
     * @throws \Exception
     */
    public function __construct()
    {
        $paymentService = new PaymentService;
        parent::__construct($paymentService);
        $this->paymentGateway = PaymentGateway::with('gatewayOptions')->where(['slug' => 'paystack'])->first();
        if (! blank($this->paymentGateway)) {
            $this->paymentGatewayOption = $this->paymentGateway->gatewayOptions->pluck('value', 'option');
            $this->publicKey = $this->paymentGatewayOption['paystack_public_key'] ?? '';
            $this->secretKey = $this->paymentGatewayOption['paystack_secret_key'] ?? '';
            $this->paymentUrl = $this->paymentGatewayOption['paystack_payment_url'] ?? 'https://api.paystack.co';
        }
        $this->client = new Client([
            'base_uri' => $this->paymentUrl,
            'headers' => [
                'Authorization' => 'Bearer ' . $this->secretKey,
                'Accept' => 'application/json',
            ],
        ]);
    }

    public function payment($order, $request)
    {
        try {
            $currencyCode = 'NGN';
            $currencyId = Settings::group('site')->get('site_default_currency');
            if (! blank($currencyId)) {
                $currency = Currency::find($currencyId);
                if ($currency) {
                    $currencyCode = $currency->code;
                }
            }

            $reference = 'ps_' . uniqid() . '_' . time();
            $payload = [
                'amount' => (int) ($order->total * 100),
                'reference' => $reference,
                'email' => $order->user?->email,
                'currency' => $currencyCode,
                'callback_url' => route('payment.success', ['order' => $order, 'paymentGateway' => 'paystack']),
                'metadata' => [
                    'order_id' => $order->id,
                    'order_serial_no' => $order->order_serial_no,
                ],
            ];

            $response = $this->client->post('/transaction/initialize', ['form_params' => $payload]);
            $data = json_decode($response->getBody(), true);
            if (isset($data['status']) && $data['status'] === true) {
                return redirect()->away($data['data']['authorization_url']);
            } else {
                return redirect()->route('payment.index', ['order' => $order, 'paymentGateway' => 'paystack'])->with(
                    'error',
                    $data['message'] ?? trans('all.message.something_wrong')
                );
            }
        } catch (Exception $e) {
            Log::error('Paystack Payment Error: ' . $e->getMessage());
            return redirect()->route('payment.index', [
                'order' => $order,
                'paymentGateway' => 'paystack',
            ])->with('error', $e->getMessage());
        }
    }

    public function status(): bool
    {
        $paymentGateways = PaymentGateway::where(['slug' => 'paystack', 'status' => Activity::ENABLE])->first();
        return (bool) $paymentGateways;
    }

    public function success($order, $request): \Illuminate\Http\RedirectResponse
    {
        try {
            $reference = $request->query('reference');
            if ($reference) {
                $response = $this->client->get("/transaction/verify/{$reference}");
                $data = json_decode($response->getBody(), true);
                if (isset($data['data']['status']) && $data['data']['status'] === 'success') {
                    $this->paymentService->payment($order, 'paystack', $reference);

                    return redirect()->route('payment.successful', ['order' => $order])
                        ->with('success', trans('all.message.payment_successful'));
                } else {
                    return redirect()->route('payment.fail', [
                        'order' => $order,
                        'paymentGateway' => 'paystack',
                    ])->with('error', trans('all.message.something_wrong'));
                }
            } else {
                return redirect()->route('payment.fail', ['order' => $order, 'paymentGateway' => 'paystack'])->with(
                    'error',
                    trans('all.message.something_wrong')
                );
            }
        } catch (Exception $e) {
            Log::error('Paystack Verify Error: ' . $e->getMessage());
            DB::rollBack();
            return redirect()->route('payment.fail', [
                'order' => $order,
                'paymentGateway' => 'paystack',
            ])->with('error', $e->getMessage());
        }
    }

    public function fail($order, $request): \Illuminate\Http\RedirectResponse
    {
        return redirect()->route('payment.index', [
            'order' => $order,
            'paymentGateway' => 'paystack',
        ])->with('error', trans('all.message.something_wrong'));
    }

    public function cancel($order, $request): \Illuminate\Http\RedirectResponse
    {
        return redirect('/checkout');
    }
}
