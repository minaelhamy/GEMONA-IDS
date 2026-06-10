<?php

namespace App\Services;


use App\Enums\Role;
use App\Enums\SwitchBox;
use App\Models\NotificationAlert;
use App\Models\Order;
use App\Models\User;
use Exception;
use Illuminate\Support\Facades\Log;

class OrderGotSmsNotificationBuilder
{
    public int $orderId;
    public object $order;

    public function __construct($orderId)
    {
        $this->orderId = $orderId;
        $this->order   = Order::find($orderId);
    }

    public function send(): void
    {
        if (!blank($this->order)) {
            $smsAdmins   = User::role(Role::ADMIN)->whereNotNull('phone')->get();
            $smsManagers = User::role(Role::MANAGER)->whereNotNull('phone')->get();

            $i         = 0;
            $smsArrays = [];
            if (!blank($smsAdmins)) {
                foreach ($smsAdmins as $smsAdmin) {
                    $smsArrays[$i] = [
                        'code'  => $smsAdmin->country_code,
                        'phone' => $smsAdmin->phone,
                    ];
                    $i++;
                }
            }

            if (!blank($smsManagers)) {
                foreach ($smsManagers as $smsManager) {
                    $smsArrays[$i] = [
                        'code'  => $smsManager->country_code,
                        'phone' => $smsManager->phone,
                    ];
                    $i++;
                }
            }

            if (count($smsArrays) > 0) {
                try {
                    $notificationAlert = NotificationAlert::where(['language' => 'admin_and_manager_new_order_message'])->first();
                    if ($notificationAlert && $notificationAlert->sms == SwitchBox::ON) {
                        $message = 'Order ID : ' . $this->order->order_serial_no . ' ' . $notificationAlert->sms_message;
                        foreach ($smsArrays as $smsArray) {
                            $this->sms($smsArray['code'], $smsArray['phone'], $message);
                        }
                    }
                } catch (Exception $e) {
                    Log::info($e->getMessage());
                }
            }
        }
    }

    private function sms($code, $phone, $message): void
    {
        try {
            $smsManagerService = new SmsManagerService();
            $smsService        = new SmsService();
            if ($smsService->gateway() && $smsManagerService->gateway($smsService->gateway())->status()) {
                $smsManagerService->gateway($smsService->gateway())->send($code, $phone, $message);
            }
        } catch (Exception $e) {
            Log::info($e->getMessage());
        }
    }
}
