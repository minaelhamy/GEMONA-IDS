<?php

namespace Database\Seeders;

use App\Models\PushNotification;
use Illuminate\Database\Seeder;
use Dipokhalder\EnvEditor\EnvEditor;

class PushNotificationTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $envService = new EnvEditor();
        if ($envService->getValue('DEMO') && $envService->getValue('DISPLAY_TYPE') == 'fashion') {
            PushNotification::insert([
                [
                    'title'       => "Hurry! Flash Sale Now Live!",
                    'description' => "Limited time offer! Grab your favorites at jaw-dropping prices. Shop now before it's gone!",
                    'role_id'     => 2,
                    'user_id'     => 0,
                    'created_at'  => now(),
                    'updated_at'  => now()
                ],
                [
                    'title'       => "Happy Birthday, VIP Customer! Here's a Gift From Us 🎁",
                    'description' => "Celebrate your day with [discount/freebie] on your next purchase. Treat yourself!",
                    'role_id'     => 2,
                    'user_id'     => 2,
                    'created_at'  => now(),
                    'updated_at'  => now()
                ],
                [
                    'title'       => "We Miss You! Here's an Exclusive Offer",
                    'description' => "It's been a while! Enjoy a special discount as our way of welcoming you back. Come rediscover what you love.",
                    'role_id'     => 0,
                    'user_id'     => 0,
                    'created_at'  => now(),
                    'updated_at'  => now()
                ],
                [
                    'title'       => "Weekly Sales Report Ready!",
                    'description' => "Get insights into this week's sales performance. View detailed analytics and trends to strategize effectively.",
                    'role_id'     => 1,
                    'user_id'     => 1,
                    'created_at'  => now(),
                    'updated_at'  => now()
                ],
                [
                    'title'       => "Festive Deals Await You!",
                    'description' => "Get into the spirit with our special holiday offers. Unwrap savings on gifts, decor, and more!",
                    'role_id'     => 3,
                    'user_id'     => 0,
                    'created_at'  => now(),
                    'updated_at'  => now()
                ],
            ]);
        }
    }
}
