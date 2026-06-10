import 'package:get/get.dart';
import 'package:shopperz/utils/constant.dart';

class ValidationRules {
  String? name(String? value) {
    if (value == null || value.isEmpty) {
      return "NAME_IS_REQUIRED".tr;
    }
    return null;
  }

  String? fullname(String? value) {
    if (value == null || value.isEmpty) {
      return "FULL_NAME_IS_REQUIRED".tr;
    }
    return null;
  }

  String? phone(String? value) {
    if (value == null || value.isEmpty) {
      return "PHONE_IS_REQUIRED".tr;
    }
    return null;
  }

  String? otp(String? value) {
    if (value == null || value.isEmpty) {
      return "OTP_IS_REQUIRED".tr;
    }
    return null;
  }

  String? country(String? value) {
    if (value == null || value.isEmpty) {
      return "COUNTRY_IS_REQUIRED".tr;
    }
    return null;
  }

  String? address(String? value) {
    if (value == null || value.isEmpty) {
      return "ADDRESS_IS_REQUIRED".tr;
    }
    return null;
  }

  email(String? value) {
    RegExp regExp = RegExp(regularExpressionEmail);
    if (value == null || value.isEmpty) {
      return "EMAIL_IS_REQUIRED".tr;
    } else if (!regExp.hasMatch(value)) {
      return 'ENTER_A_VALID_EMAIL'.tr;
    } else if (!value.contains('@')) {
      return "ENTER_A_VALID_EMAIL".tr;
    } else {
      return null;
    }
  }

  password(String? value) {
    if (value == null || value.isEmpty) {
      return "PASSWORD_IS_REQUIRED".tr;
    } else if (value.length <= 5) {
      return "PASSWORD_MUST_BE_AT_LEAST_6_CHARACTERS".tr;
    } else {
      return null;
    }
  }
}
