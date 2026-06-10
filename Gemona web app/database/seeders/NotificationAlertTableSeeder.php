<?php

namespace Database\Seeders;

use App\Enums\SwitchBox;
use Illuminate\Database\Seeder;
use App\Models\NotificationAlert;

class NotificationAlertTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public array $notificationAlerts = [
        'name'    => [
            'Order Pending Message',
            'Order Confirmation Message',
            'Order On The Way Message',
            'Order Delivered Message',
            'Order Canceled Message',
            'Order Rejected Message',
            'Admin And Manager New Order Message',
        ],
        'message' => [
            'Your order is successfully placed.',
            'Your order is confirmed.',
            'Your order is on the way.',
            'Your order is successfully delivered.',
            'Your order is canceled.',
            'Your order is rejected.',
            'You have a new order.',
        ]

    ];

    public function run()
    {
        foreach ($this->notificationAlerts['name'] as $key => $notificationAlert) {
            NotificationAlert::create([
                'name'                      => $notificationAlert,
                'language'                  => str_replace(' ', '_', strtolower($notificationAlert)),
                'mail_message'              => $this->notificationAlerts['message'][$key],
                'sms_message'               => $this->notificationAlerts['message'][$key],
                'push_notification_message' => $this->notificationAlerts['message'][$key],
                'mail'                      => SwitchBox::OFF,
                'sms'                       => SwitchBox::OFF,
                'push_notification'         => SwitchBox::OFF,
            ]);
        }
    }
}
