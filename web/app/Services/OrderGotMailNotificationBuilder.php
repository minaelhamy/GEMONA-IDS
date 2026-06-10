<?php

namespace App\Services;


use App\Enums\Role;
use App\Enums\SwitchBox;
use App\Mail\OrderGotMail;
use App\Models\NotificationAlert;
use App\Models\Order;
use App\Models\User;
use Exception;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class OrderGotMailNotificationBuilder
{
    public int $orderId;
    public object $order;

    public function __construct($orderId)
    {
        $this->orderId = $orderId;
        $this->order   = Order::find($orderId);
    }

    public function send()
    {
        if (!blank($this->order)) {
            $emailAdmins   = User::role(Role::ADMIN)->whereNotNull('email')->get();
            $emailManagers = User::role(Role::MANAGER)->whereNotNull('email')->get();

            $i          = 0;
            $emailArray = [];
            if (!blank($emailAdmins)) {
                foreach ($emailAdmins as $emailAdmin) {
                    $emailArray[$i] = $emailAdmin->email;
                    $i++;
                }
            }

            if (!blank($emailManagers)) {
                foreach ($emailManagers as $emailManager) {
                    $emailArray[$i] = $emailManager->email;
                    $i++;
                }
            }

            if (count($emailArray) > 0) {
                try {
                    $notificationAlert = NotificationAlert::where(['language' => 'admin_and_manager_new_order_message'])->first();
                    if ($notificationAlert && $notificationAlert->mail == SwitchBox::ON) {
                        try {
                            Mail::to($emailArray[0])->cc($emailArray)->send(new OrderGotMail($this->order->order_serial_no, $notificationAlert->mail_message));
                        } catch (Exception $e) {
                            Log::info($e->getMessage());
                        }
                    }
                } catch (Exception $e) {
                    Log::info($e->getMessage());
                }
            }

        }
    }
}
