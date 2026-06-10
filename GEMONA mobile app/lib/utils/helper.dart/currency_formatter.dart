import 'package:get/get.dart';
import 'package:shopperz/app/modules/auth/controller/auth_controler.dart';

String formatPriceWithCurrency(double price) {
  final currency =
      Get.find<AuthController>().settingModel!.data?.siteDefaultCurrencySymbol
          .toString() ??
      "";
  final position = Get.find<AuthController>()
      .settingModel!
      .data
      ?.siteCurrencyPosition
      .toString();

  final formattedPrice = price.toStringAsFixed(2);

  return position == "5"
      ? '$currency$formattedPrice'
      : '$formattedPrice$currency';
}
