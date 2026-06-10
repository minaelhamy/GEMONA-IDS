<?php

namespace App\Http\Resources;

use App\Libraries\AppLibrary;
use App\Models\ThemeSetting;
use Illuminate\Http\Resources\Json\JsonResource;

class SettingResource extends JsonResource
{

    public array $info;

    public function __construct($info)
    {
        parent::__construct($info);
        $this->info = $info;
    }

    public function toArray($request): array
    {
        return [
            'app_version'                           => AppLibrary::appVersion(),
            'company_name'                          => $this->info['company_name'],
            'company_email'                         => $this->info['company_email'],
            "company_calling_code"                  => $this->info['company_calling_code'],
            'company_phone'                         => $this->info['company_phone'],
            'company_country_code'                  => $this->info['company_country_code'],
            'company_address'                       => $this->info['company_address'],
            'site_default_language'                 => $this->info['site_default_language'],
            'site_android_app_link'                 => $this->info['site_android_app_link'],
            'site_ios_app_link'                     => $this->info['site_ios_app_link'],
            'site_copyright'                        => $this->info['site_copyright'],
            'site_currency_position'                => $this->info['site_currency_position'],
            'site_digit_after_decimal_point'        => $this->info['site_digit_after_decimal_point'],
            'site_default_currency_symbol'          => $this->info['site_default_currency_symbol'],
            'site_phone_verification'               => $this->info['site_phone_verification'],
            'site_email_verification'               => $this->info['site_email_verification'],
            'site_language_switch'                  => $this->info['site_language_switch'],
            'site_online_payment_gateway'           => $this->info['site_online_payment_gateway'],
            'site_cash_on_delivery'                 => $this->info['site_cash_on_delivery'],
            'shipping_setup_method'                 => $this->info['shipping_setup_method'],
            'shipping_setup_flat_rate_wise_cost'    => $this->info['shipping_setup_flat_rate_wise_cost'],
            'shipping_setup_area_wise_default_cost' => $this->info['shipping_setup_area_wise_default_cost'],
            'theme_logo'                            => $this->themeImage('theme_logo')->logo,
            'theme_footer_logo'                     => $this->themeImage('theme_footer_logo')->footerLogo,
            'theme_favicon_logo'                    => $this->themeImage('theme_favicon_logo')->faviconLogo,
            'otp_type'                              => $this->info['otp_type'],
            'otp_digit_limit'                       => $this->info['otp_digit_limit'],
            'otp_expire_time'                       => $this->info['otp_expire_time'],
            'social_media_facebook'                 => $this->info['social_media_facebook'],
            'social_media_instagram'                => $this->info['social_media_instagram'],
            'social_media_twitter'                  => $this->info['social_media_twitter'],
            'social_media_youtube'                  => $this->info['social_media_youtube'],
            'cookies_details_page_id'               => $this->info['cookies_details_page_id'],
            'cookies_summary'                       => $this->info['cookies_summary'],
            'notification_fcm_api_key'              => $this->info['notification_fcm_api_key'],
            'notification_fcm_auth_domain'          => $this->info['notification_fcm_auth_domain'],
            'notification_fcm_project_id'           => $this->info['notification_fcm_project_id'],
            'notification_fcm_storage_bucket'       => $this->info['notification_fcm_storage_bucket'],
            'notification_fcm_messaging_sender_id'  => $this->info['notification_fcm_messaging_sender_id'],
            'notification_fcm_app_id'               => $this->info['notification_fcm_app_id'],
            'notification_fcm_measurement_id'       => $this->info['notification_fcm_measurement_id'],
            'notification_fcm_public_vapid_key'     => $this->info['notification_fcm_public_vapid_key'],
            'notification_audio'                    => asset('/audio/notification.mp3'),
            'image_cart'                            => asset('/images/required/empty-cart.gif'),
            'image_wishlist'                        => asset('/images/required/empty-wishlist.gif'),
            'image_app_store'                       => asset('/images/required/app-store.png'),
            'image_play_store'                      => asset('/images/required/play-store.png'),
            'image_confirm'                         => asset('/images/required/confirm.gif'),
            'image_403'                             => asset('/images/required/403.png'),
            'image_404'                             => asset('/images/required/404.png'),
            'not_found'                             => asset('/images/default/not-found/not_found.png')
        ];
    }

    public function themeImage($key)
    {
        return ThemeSetting::where(['key' => $key])->first();
    }
}
