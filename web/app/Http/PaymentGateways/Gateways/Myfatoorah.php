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
use Dipokhalder\Settings\Facades\Settings;
use App\Models\CapturePaymentNotification;
use Illuminate\Http\Request;
use MyFatoorah\Library\MyFatoorah as MyFatoorahClient;
use MyFatoorah\Library\API\Payment\MyFatoorahPayment;
use MyFatoorah\Library\API\Payment\MyFatoorahPaymentStatus;

class Myfatoorah extends PaymentAbstract
{
    protected $config = [];
    protected $countryCode = '';
    protected $gatewayErrorMessage = '';

    public function __construct()
    {
        $paymentService = new PaymentService();
        parent::__construct($paymentService);

        $this->countryCode    = Settings::group('company')->get('company_country_code');
        $this->paymentGateway = PaymentGateway::with('gatewayOptions')->where(['slug' => 'myfatoorah'])->first();

        $this->paymentGatewayOption    = $this->paymentGateway->gatewayOptions->pluck('value', 'option');
        $this->config = [
            'apiKey' => $this->paymentGatewayOption['myfatoorah_api_key'],
            'vcCode' => $this->countryCode,
            'isTest' => $this->paymentGatewayOption['myfatoorah_mode'] == GatewayMode::SANDBOX ? true : false,
        ];
    }

    public function payment ($order, $request) : \Illuminate\Routing\Redirector|\Illuminate\Contracts\Foundation\Application|\Illuminate\Http\RedirectResponse
    {
        try {
            $this->gateway = new MyFatoorahPayment($this->config);
        } catch (Exception $e) {
            $this->gatewayErrorMessage = $e->getMessage();
            Log::error('MyFatoorah initialization failed: ' . $this->gatewayErrorMessage);
            return redirect()->route('payment.index', ['order' => $order, 'paymentGateway' => 'myfatoorah'])->with('error', $this->gatewayErrorMessage);
        }

        try {
            $response = $this->gateway->sendPayment([
                'InvoiceValue'       => $order->total,
                'CustomerName'       => $order->user->name,
                'NotificationOption' => 'LNK',
                'CustomerReference'  => $order->order_serial_no,
                'CustomerMobile'     => $order->user->phone,
                'CustomerEmail'      => $order->user->email,
                'CallBackUrl'        => route('payment.success', ['order' => $order, 'paymentGateway' => 'myfatoorah']),
                'ErrorUrl'           => route('payment.fail', ['order' => $order, 'paymentGateway' => 'myfatoorah']),
            ]);
       
            if (isset($response->InvoiceId) && isset($response->InvoiceURL)) {
                $capturePaymentNotification = DB::table('capture_payment_notifications')->where([
                    ['order_id', $order->id]
                ]);
                $capturePaymentNotification?->delete();

                $token = $response->InvoiceId;
                CapturePaymentNotification::create([
                    'order_id'   => $order->id,
                    'token'      => $token,
                    'created_at' => now()
                ]);
                return redirect()->away($response->InvoiceURL);
            } else {
                return redirect()->route('payment.index', ['order' => $order, 'paymentGateway' => 'myfatoorah'])->with('error', trans('all.message.something_wrong'));
            }
        } catch (Exception $e) {
            $this->gatewayErrorMessage = $e->getMessage();
            Log::error('MyFatoorah payment error : ' . $this->gatewayErrorMessage);
            return redirect()->route('payment.index', ['order' => $order,'paymentGateway' => 'myfatoorah'])->with('error', $this->gatewayErrorMessage);
        }
    }

    public function status () : bool
    {
        $paymentGateways = PaymentGateway::where(['slug' => 'myfatoorah', 'status' => Activity::ENABLE])->first();
        if ($paymentGateways) {
            return true;
        }
        return false;
    }

    function paymentStatus ($keyID, $keyType = 10) : object
    {
        switch ($keyType) {
            case 10 :
                $keyType = "InvoiceId";
                break;
            case 20 :
                $keyType = "PaymentId";
                break;
            case 30 :
                $keyType = "CustomerReference";
                break;
            
            default:
                $keyType = "InvoiceId";
                break;
        }

        try {
            $mfObj = new MyFatoorahPaymentStatus($this->config);
            $data  = $mfObj->getPaymentStatus($keyID, $keyType);
            return $data;
        } catch (Exception $e) {
            $this->gatewayErrorMessage = $e->getMessage();
            Log::error('MyFatoorah Status Check Failed : ' . $this->gatewayErrorMessage);
            return redirect()->back()->with('error', $this->gatewayErrorMessage);
        }
    }

    public function success ($order, $request) : \Illuminate\Http\RedirectResponse
    {
        try {
            $capturePaymentNotification = DB::table('capture_payment_notifications')->where([
                ['order_id', $order->id]
            ]);
            $token = $capturePaymentNotification->first();
            if (!blank($token) && $order->id == $token->order_id) {
                $paymentResponse = $this->paymentStatus($request->paymentId, 20);
                if ($paymentResponse->InvoiceStatus === "Paid") {
                    DB::transaction(function () use ($order, $request, $token, $capturePaymentNotification) {
                        $this->paymentService->payment($order, 'myfatoorah', $request->paymentId);
                        $capturePaymentNotification->delete();
                    });
                    return redirect()->route('payment.successful', ['order' => $order])->with('success', trans('all.message.payment_successful'));
                }
            }

            return redirect()->route('payment.index', ['order' => $order,'paymentGateway' => 'myfatoorah'])->with('error', trans('all.message.something_wrong'));
        } catch (Exception $e) {
            $this->gatewayErrorMessage = $e->getMessage();
            Log::error("MyFatoorah success error : ". $this->gatewayErrorMessage);
            return redirect()->route('payment.index', ['order' => $order,'paymentGateway' => 'myfatoorah'])->with('error', $this->gatewayErrorMessage);
        }
    }

    public function fail ($order, $request) : \Illuminate\Http\RedirectResponse
    {
        $paymentResponse = $this->paymentStatus($request->paymentId, 20);
        $this->gatewayErrorMessage = $paymentResponse->InvoiceStatus . ' : ' . $paymentResponse->InvoiceError;
        return redirect()->route('payment.index', ['order' => $order,'paymentGateway' => 'myfatoorah'])->with('error', $this->gatewayErrorMessage);
    }

    public function cancel($order, $request): \Illuminate\Http\RedirectResponse
    {
        return redirect('/checkout/payment');
    }
}