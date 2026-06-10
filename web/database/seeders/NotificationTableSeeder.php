<?php

namespace Database\Seeders;


use Illuminate\Database\Seeder;
use App\Models\NotificationSetting;
use Dipokhalder\EnvEditor\EnvEditor;
use Dipokhalder\Settings\Facades\Settings;


class NotificationTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $envService = new EnvEditor();
        Settings::group('notification')->set([
            'notification_fcm_public_vapid_key'    => '',
            'notification_fcm_api_key'             => '',
            'notification_fcm_auth_domain'         => '',
            'notification_fcm_project_id'          => '',
            'notification_fcm_storage_bucket'      => '',
            'notification_fcm_messaging_sender_id' => '',
            'notification_fcm_app_id'              => '',
            'notification_fcm_measurement_id'      => '',
            'notification_fcm_json_file'           => '',
        ]);

        if ($envService->getValue('DEMO') && file_exists(public_path('/file/service-account-file.json'))) {
            $setting = NotificationSetting::where('key', 'notification_fcm_json_file')->first();
            $setting->addMedia(public_path('/file/service-account-file.json'))->preservingOriginal()->usingFileName('service-account-file.json')->toMediaCollection('notification-file');
        }
    }
}
