<?php

namespace App\Services;


use App\Enums\OrderStatus;
use App\Enums\SwitchBox;
use App\Mail\OrderMail;
use App\Models\NotificationAlert;
use App\Models\Order;
use App\Models\User;
use Exception;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class OrderMailNotificationBuilder
{
    public int $orderId;
    public mixed $status;
    public object $order;

    public function __construct($orderId, $status)
    {
        $this->orderId = $orderId;
        $this->status  = $status;
        $this->order   = Order::find($orderId);
    }

    public function send(): void
    {
        if (!blank($this->order)) {
            $user = User::find($this->order->user_id);
            if (!blank($user)) {
                if ($user->email) {
                    $this->message($user->name, $user->email, $this->status, $this->order->order_serial_no);
                }
            }
        }
    }

    private function message($name, $email, $status, $orderId): void
    {
        if ($status == OrderStatus::PENDING) {
            $this->pending($name, $email, $orderId);
        } elseif ($status == OrderStatus::CONFIRMED) {
            $this->confirmed($name, $email, $orderId);
        } elseif ($status == OrderStatus::ON_THE_WAY) {
            $this->onTheWay($name, $email, $orderId);
        } elseif ($status == OrderStatus::DELIVERED) {
            $this->delivered($name, $email, $orderId);
        } elseif ($status == OrderStatus::CANCELED) {
            $this->canceled($name, $email, $orderId);
        } elseif ($status == OrderStatus::REJECTED) {
            $this->rejected($name, $email, $orderId);
        }
    }

    private function mail($name, $email, $orderId, $message): void
    {
        try {
            Mail::to($email)->send(new OrderMail($name, $orderId, $message));
        } catch (Exception $e) {
            Log::info($e->getMessage());
        }
    }

    private function pending($name, $email, $orderId): void
    {
        $notificationAlert = NotificationAlert::where(['language' => 'order_pending_message'])->first();
        if ($notificationAlert && $notificationAlert->mail == SwitchBox::ON) {
            $this->mail($name, $email, $orderId, $notificationAlert->mail_message);
        }
    }

    private function confirmed($name, $email, $orderId): void
    {
        $notificationAlert = NotificationAlert::where(['language' => 'order_confirmation_message'])->first();
        if ($notificationAlert && $notificationAlert->mail == SwitchBox::ON) {
            $this->mail($name, $email, $orderId, $notificationAlert->mail_message);
        }
    }

    private function onTheWay($name, $email, $orderId): void
    {
        $notificationAlert = NotificationAlert::where(['language' => 'order_on_the_way_message'])->first();
        if ($notificationAlert && $notificationAlert->mail == SwitchBox::ON) {
            $this->mail($name, $email, $orderId, $notificationAlert->mail_message);
        }
    }

    private function delivered($name, $email, $orderId): void
    {
        $notificationAlert = NotificationAlert::where(['language' => 'order_delivered_message'])->first();
        if ($notificationAlert && $notificationAlert->mail == SwitchBox::ON) {
            $this->mail($name, $email, $orderId, $notificationAlert->mail_message);
        }
    }

    private function canceled($name, $email, $orderId): void
    {
        $notificationAlert = NotificationAlert::where(['language' => 'order_canceled_message'])->first();
        if ($notificationAlert && $notificationAlert->mail == SwitchBox::ON) {
            $this->mail($name, $email, $orderId, $notificationAlert->mail_message);
        }
    }

    private function rejected($name, $email, $orderId): void
    {
        $notificationAlert = NotificationAlert::where(['language' => 'order_rejected_message'])->first();
        if ($notificationAlert && $notificationAlert->mail == SwitchBox::ON) {
            $this->mail($name, $email, $orderId, $notificationAlert->mail_message);
        }
    }
}