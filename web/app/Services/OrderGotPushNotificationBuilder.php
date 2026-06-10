<?php

namespace App\Services;


use App\Enums\Role;
use App\Enums\SwitchBox;
use App\Models\NotificationAlert;
use App\Models\Order;
use App\Models\User;
use Exception;
use Illuminate\Support\Facades\Log;

class OrderGotPushNotificationBuilder
{
    public int $orderId;
    public object $order;

    public function __construct($orderId,)
    {
        $this->orderId = $orderId;
        $this->order   = Order::find($orderId);
    }

    public function send(): void
    {
        if (!blank($this->order)) {
            $fcmWebDeviceTokenAdmins      = User::role(Role::ADMIN)->whereNotNull('web_token')->get();
            $fcmMobileDeviceTokenAdmins   = User::role(Role::ADMIN)->whereNotNull('device_token')->get();
            $fcmWebDeviceTokenManagers    = User::role(Role::MANAGER)->whereNotNull('web_token')->get();
            $fcmMobileDeviceTokenManagers = User::role(Role::MANAGER)->whereNotNull('device_token')->get();

            $i             = 0;
            $fcmTokenArray = [];
            if (!blank($fcmWebDeviceTokenAdmins)) {
                foreach ($fcmWebDeviceTokenAdmins as $fcmWebDeviceTokenAdmin) {
                    $fcmTokenArray[$i] = $fcmWebDeviceTokenAdmin->web_token;
                    $i++;
                }
            }

            if (!blank($fcmMobileDeviceTokenAdmins)) {
                foreach ($fcmMobileDeviceTokenAdmins as $fcmMobileDeviceTokenAdmin) {
                    $fcmTokenArray[$i] = $fcmMobileDeviceTokenAdmin->web_token;
                    $i++;
                }
            }

            if (!blank($fcmWebDeviceTokenManagers)) {
                foreach ($fcmWebDeviceTokenManagers as $fcmWebDeviceTokenManager) {
                    $fcmTokenArray[$i] = $fcmWebDeviceTokenManager->web_token;
                    $i++;
                }
            }

            if (!blank($fcmMobileDeviceTokenManagers)) {
                foreach ($fcmMobileDeviceTokenManagers as $fcmMobileDeviceTokenManager) {
                    $fcmTokenArray[$i] = $fcmMobileDeviceTokenManager->device_token;
                    $i++;
                }
            }

            if (count($fcmTokenArray) > 0) {
                try {
                    $notificationAlert = NotificationAlert::where(['language' => 'admin_and_manager_new_order_message'])->first();
                    if ($notificationAlert && $notificationAlert->push_notification == SwitchBox::ON) {
                        $pushNotification = (object)[
                            'title'       => 'New Order Notification',
                            'description' => $notificationAlert->push_notification_message,
                            'order_id'    => $this->orderId
                        ];
                        $firebase         = new FirebaseService();
                        $firebase->sendNotification($pushNotification, $fcmTokenArray, "new-order-found");
                    }
                } catch (Exception $e) {
                    Log::info($e->getMessage());
                }
            }

        }
    }
}
