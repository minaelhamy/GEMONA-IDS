class SettingModel {
  Data? data;

  SettingModel({this.data});

  SettingModel.fromJson(Map<String, dynamic> json) {
    data = json['data'] != null ? Data.fromJson(json['data']) : null;
  }

  Map<String, dynamic> toJson() {
    final Map<String, dynamic> data = <String, dynamic>{};
    if (this.data != null) {
      data['data'] = this.data!.toJson();
    }
    return data;
  }
}

class Data {
  String? companyName;
  String? companyEmail;
  String? companyCallingCode;
  String? companyPhone;
  String? companyCountryCode;
  String? companyAddress;
  int? siteDefaultLanguage;
  String? siteAndroidAppLink;
  String? siteIosAppLink;
  String? siteCopyright;
  int? siteCurrencyPosition;
  String? siteDigitAfterDecimalPoint;
  String? siteDefaultCurrencySymbol;
  int? sitePhoneVerification;
  int? siteEmailVerification;
  int? siteLanguageSwitch;
  int? siteOnlinePaymentGateway;
  int? siteCashOnDelivery;
  int? shippingSetupMethod;
  String? shippingSetupFlatRateWiseCost;
  String? shippingSetupAreaWiseDefaultCost;
  String? themeLogo;
  String? themeFooterLogo;
  String? themeFaviconLogo;
  String? otpType;
  String? otpDigitLimit;
  String? otpExpireTime;
  String? socialMediaFacebook;
  String? socialMediaInstagram;
  String? socialMediaTwitter;
  String? socialMediaYoutube;
  int? cookiesDetailsPageId;
  String? cookiesSummary;
  String? notificationFcmApiKey;
  String? notificationFcmAuthDomain;
  String? notificationFcmProjectId;
  String? notificationFcmStorageBucket;
  String? notificationFcmMessagingSenderId;
  String? notificationFcmAppId;
  String? notificationFcmMeasurementId;
  String? notificationFcmPublicVapidKey;
  String? notificationAudio;
  String? imageCart;
  String? imageWishlist;
  String? imageAppStore;
  String? imagePlayStore;
  String? imageConfirm;

  Data(
      {this.companyName,
      this.companyEmail,
      this.companyCallingCode,
      this.companyPhone,
      this.companyCountryCode,
      this.companyAddress,
      this.siteDefaultLanguage,
      this.siteAndroidAppLink,
      this.siteIosAppLink,
      this.siteCopyright,
      this.siteCurrencyPosition,
      this.siteDigitAfterDecimalPoint,
      this.siteDefaultCurrencySymbol,
      this.sitePhoneVerification,
      this.siteEmailVerification,
      this.siteLanguageSwitch,
      this.siteOnlinePaymentGateway,
      this.siteCashOnDelivery,
      this.shippingSetupMethod,
      this.shippingSetupFlatRateWiseCost,
      this.shippingSetupAreaWiseDefaultCost,
      this.themeLogo,
      this.themeFooterLogo,
      this.themeFaviconLogo,
      this.otpType,
      this.otpDigitLimit,
      this.otpExpireTime,
      this.socialMediaFacebook,
      this.socialMediaInstagram,
      this.socialMediaTwitter,
      this.socialMediaYoutube,
      this.cookiesDetailsPageId,
      this.cookiesSummary,
      this.notificationFcmApiKey,
      this.notificationFcmAuthDomain,
      this.notificationFcmProjectId,
      this.notificationFcmStorageBucket,
      this.notificationFcmMessagingSenderId,
      this.notificationFcmAppId,
      this.notificationFcmMeasurementId,
      this.notificationFcmPublicVapidKey,
      this.notificationAudio,
      this.imageCart,
      this.imageWishlist,
      this.imageAppStore,
      this.imagePlayStore,
      this.imageConfirm});

  Data.fromJson(Map<String, dynamic> json) {
    companyName = json['company_name'];
    companyEmail = json['company_email'];
    companyCallingCode = json['company_calling_code'];
    companyPhone = json['company_phone'];
    companyCountryCode = json['company_country_code'];
    companyAddress = json['company_address'];
    siteDefaultLanguage = json['site_default_language'];
    siteAndroidAppLink = json['site_android_app_link'];
    siteIosAppLink = json['site_ios_app_link'];
    siteCopyright = json['site_copyright'];
    siteCurrencyPosition = json['site_currency_position'];
    siteDigitAfterDecimalPoint = json['site_digit_after_decimal_point'];
    siteDefaultCurrencySymbol = json['site_default_currency_symbol'];
    sitePhoneVerification = json['site_phone_verification'];
    siteEmailVerification = json['site_email_verification'];
    siteLanguageSwitch = json['site_language_switch'];
    siteOnlinePaymentGateway = json['site_online_payment_gateway'];
    siteCashOnDelivery = json['site_cash_on_delivery'];
    shippingSetupMethod = json['shipping_setup_method'];
    shippingSetupFlatRateWiseCost = json['shipping_setup_flat_rate_wise_cost'];
    shippingSetupAreaWiseDefaultCost =
        json['shipping_setup_area_wise_default_cost'];
    themeLogo = json['theme_logo'];
    themeFooterLogo = json['theme_footer_logo'];
    themeFaviconLogo = json['theme_favicon_logo'];
    otpType = json['otp_type'];
    otpDigitLimit = json['otp_digit_limit'];
    otpExpireTime = json['otp_expire_time'];
    socialMediaFacebook = json['social_media_facebook'];
    socialMediaInstagram = json['social_media_instagram'];
    socialMediaTwitter = json['social_media_twitter'];
    socialMediaYoutube = json['social_media_youtube'];
    cookiesDetailsPageId = json['cookies_details_page_id'];
    cookiesSummary = json['cookies_summary'];
    notificationFcmApiKey = json['notification_fcm_api_key'];
    notificationFcmAuthDomain = json['notification_fcm_auth_domain'];
    notificationFcmProjectId = json['notification_fcm_project_id'];
    notificationFcmStorageBucket = json['notification_fcm_storage_bucket'];
    notificationFcmMessagingSenderId =
        json['notification_fcm_messaging_sender_id'];
    notificationFcmAppId = json['notification_fcm_app_id'];
    notificationFcmMeasurementId = json['notification_fcm_measurement_id'];
    notificationFcmPublicVapidKey = json['notification_fcm_public_vapid_key'];
    notificationAudio = json['notification_audio'];
    imageCart = json['image_cart'];
    imageWishlist = json['image_wishlist'];
    imageAppStore = json['image_app_store'];
    imagePlayStore = json['image_play_store'];
    imageConfirm = json['image_confirm'];
  }

  Map<String, dynamic> toJson() {
    final Map<String, dynamic> data = <String, dynamic>{};
    data['company_name'] = companyName;
    data['company_email'] = companyEmail;
    data['company_calling_code'] = companyCallingCode;
    data['company_phone'] = companyPhone;
    data['company_country_code'] = companyCountryCode;
    data['company_address'] = companyAddress;
    data['site_default_language'] = siteDefaultLanguage;
    data['site_android_app_link'] = siteAndroidAppLink;
    data['site_ios_app_link'] = siteIosAppLink;
    data['site_copyright'] = siteCopyright;
    data['site_currency_position'] = siteCurrencyPosition;
    data['site_digit_after_decimal_point'] = siteDigitAfterDecimalPoint;
    data['site_default_currency_symbol'] = siteDefaultCurrencySymbol;
    data['site_phone_verification'] = sitePhoneVerification;
    data['site_email_verification'] = siteEmailVerification;
    data['site_language_switch'] = siteLanguageSwitch;
    data['site_online_payment_gateway'] = siteOnlinePaymentGateway;
    data['site_cash_on_delivery'] = siteCashOnDelivery;
    data['shipping_setup_method'] = shippingSetupMethod;
    data['shipping_setup_flat_rate_wise_cost'] =
        shippingSetupFlatRateWiseCost;
    data['shipping_setup_area_wise_default_cost'] =
        shippingSetupAreaWiseDefaultCost;
    data['theme_logo'] = themeLogo;
    data['theme_footer_logo'] = themeFooterLogo;
    data['theme_favicon_logo'] = themeFaviconLogo;
    data['otp_type'] = otpType;
    data['otp_digit_limit'] = otpDigitLimit;
    data['otp_expire_time'] = otpExpireTime;
    data['social_media_facebook'] = socialMediaFacebook;
    data['social_media_instagram'] = socialMediaInstagram;
    data['social_media_twitter'] = socialMediaTwitter;
    data['social_media_youtube'] = socialMediaYoutube;
    data['cookies_details_page_id'] = cookiesDetailsPageId;
    data['cookies_summary'] = cookiesSummary;
    data['notification_fcm_api_key'] = notificationFcmApiKey;
    data['notification_fcm_auth_domain'] = notificationFcmAuthDomain;
    data['notification_fcm_project_id'] = notificationFcmProjectId;
    data['notification_fcm_storage_bucket'] = notificationFcmStorageBucket;
    data['notification_fcm_messaging_sender_id'] =
        notificationFcmMessagingSenderId;
    data['notification_fcm_app_id'] = notificationFcmAppId;
    data['notification_fcm_measurement_id'] = notificationFcmMeasurementId;
    data['notification_fcm_public_vapid_key'] =
        notificationFcmPublicVapidKey;
    data['notification_audio'] = notificationAudio;
    data['image_cart'] = imageCart;
    data['image_wishlist'] = imageWishlist;
    data['image_app_store'] = imageAppStore;
    data['image_play_store'] = imagePlayStore;
    data['image_confirm'] = imageConfirm;
    return data;
  }
}
