import 'dart:io';
import 'package:flutter/material.dart';
import 'package:flutter/services.dart';
import 'package:flutter_inappwebview/flutter_inappwebview.dart';
import 'package:flutter_screenutil/flutter_screenutil.dart';
import 'package:flutter_svg/svg.dart';
import 'package:get/get.dart';
import 'package:shopperz/app/modules/cart/controller/cart_controller.dart';
import 'package:shopperz/app/modules/order/views/order_history_screen.dart';
import 'package:shopperz/app/modules/payment/views/payment_failed_screen.dart';
import 'package:shopperz/config/theme/app_color.dart';
import 'package:shopperz/utils/api_list.dart';
import 'package:shopperz/utils/images.dart';
import 'package:shopperz/utils/svg_icon.dart';
import 'package:shopperz/widgets/custom_snackbar.dart';
import 'package:shopperz/widgets/primary_button.dart';
import 'package:shopperz/widgets/textwidget.dart';
import '../../navbar/controller/navbar_controller.dart';
import '../../navbar/views/navbar_view.dart';

class PaymentView extends StatefulWidget {
  final int? orderId;
  final String? slug;
  const PaymentView({super.key, this.orderId, this.slug});
  @override
  _PaymentViewState createState() => _PaymentViewState();
}

class _PaymentViewState extends State<PaymentView> {
  String? selectedUrl;
  bool isLoading = true;
  MyInAppBrowser? browser;

  @override
  void initState() {
    super.initState();
    selectedUrl =
        "${ApiList.baseUrl}/payment/${widget.slug}/pay/${widget.orderId}";
    _initData();
  }

  void _initData() async {
    browser = MyInAppBrowser();

    if (Platform.isAndroid) {
      await InAppWebViewController.setWebContentsDebuggingEnabled(true);
    }

    await browser?.openUrlRequest(
      urlRequest: URLRequest(url: WebUri(selectedUrl!)),
      settings: InAppBrowserClassSettings(
        browserSettings: InAppBrowserSettings(
          hideUrlBar: true,
          hideToolbarTop: true,
        ),
        webViewSettings: InAppWebViewSettings(
          javaScriptEnabled: true,
          useShouldOverrideUrlLoading: true,
          allowsInlineMediaPlayback: true,
          allowsBackForwardNavigationGestures:
              true, // Enables navigation gestures for iOS
        ),
      ),
    );
  }

  @override
  Widget build(BuildContext context) {
    return AnnotatedRegion<SystemUiOverlayStyle>(
      value: const SystemUiOverlayStyle(
        systemNavigationBarColor: Colors.white,
        systemNavigationBarIconBrightness: Brightness.dark,
        statusBarIconBrightness: Brightness.dark,
        statusBarColor: Colors.transparent,
        statusBarBrightness: Brightness.dark,
      ),
      child: Scaffold(
        backgroundColor: AppColor.whiteColor,
        body: Center(
          child: Stack(
            children: [
              isLoading
                  ? const Center(
                    child: CircularProgressIndicator(
                      valueColor: AlwaysStoppedAnimation<Color>(
                        AppColor.primaryColor,
                      ),
                    ),
                  )
                  : const SizedBox.shrink(),
            ],
          ),
        ),
      ),
    );
  }
}

class MyInAppBrowser extends InAppBrowser {
  bool _canRedirect = true;

  @override
  Future onBrowserCreated() async {}

  @override
  Future onLoadStart(url) async {
    if (url != null) _pageRedirect(url.toString());
  }

  @override
  Future onLoadStop(url) async {
    if (url != null) _pageRedirect(url.toString());
  }

  @override
  void onLoadError(url, code, message) {}

  @override
  void onUpdateVisitedHistory(Uri? url, bool? isReload) async {
    if (url != null) _pageRedirect(url.toString());
  }

  @override
  Future<NavigationActionPolicy> shouldOverrideUrlLoading(
    navigationAction,
  ) async {
    final uri = navigationAction.request.url;
    if (uri != null) _pageRedirect(uri.toString());
    return NavigationActionPolicy.ALLOW;
  }

  @override
  void onExit() {
    if (_canRedirect) {
      showDialog(
        context: Get.context!,
        barrierDismissible: false,
        builder: (BuildContext context) {
          return PopScope(
            canPop: false,
            onPopInvokedWithResult: (_, dynamic) async => false,
            child: AlertDialog(
              contentPadding: EdgeInsets.all(10.r),
              content: const PaymentFailedView(),
            ),
          );
        },
      );
    }
  }

  void _pageRedirect(String url) async {
    Future.delayed(const Duration(milliseconds: 500), () {
      if (_canRedirect) {
        bool isSuccess =
            url.contains('success') && url.contains(ApiList.baseUrl);
        bool isFailed = url.contains('fail') && url.contains(ApiList.baseUrl);
        bool isCancel = url.contains('cancel') && url.contains(ApiList.baseUrl);
        bool isBack =
            url.contains('checkout/payment') && url.contains(ApiList.baseUrl);

        if (isSuccess || isFailed || isCancel || isBack) {
          _canRedirect = false;
          close();
        }

        if (isSuccess) {
          _canRedirect = false;
          close();

          final navController = Get.put(NavbarController());
          Get.offAll(() => const NavBarView());
          navController.selectPage(0);

          Get.dialog(
            barrierDismissible: false,
            Dialog(
              insetPadding: EdgeInsets.all(10.r),
              backgroundColor: Colors.transparent,
              child: Stack(
                children: [
                  Container(
                    height: 318.h,
                    width: 328.w,
                    decoration: BoxDecoration(
                      color: AppColor.whiteColor,
                      borderRadius: BorderRadius.circular(16.r),
                    ),
                    child: Padding(
                      padding: EdgeInsets.all(16.r),
                      child: Column(
                        mainAxisAlignment: MainAxisAlignment.spaceEvenly,
                        crossAxisAlignment: CrossAxisAlignment.center,
                        children: [
                          TextWidget(
                            text: 'Thank you for your order!'.tr,
                            color: AppColor.textColor,
                            fontSize: 16.sp,
                            fontWeight: FontWeight.w500,
                          ),
                          Image.asset(
                            AppImages.oderConfirm,
                            height: 120.h,
                            width: 120.w,
                          ),
                          TextWidget(
                            text: 'Your order is confirmed.'.tr,
                            color: AppColor.textColor,
                            fontSize: 16.sp,
                            fontWeight: FontWeight.w500,
                          ),
                          InkWell(
                            onTap: () {
                              Get.off(() => const OrderHistoryScreen());
                            },
                            child: PrimaryButton(
                              text: 'Go to order details'.tr,
                            ),
                          ),
                        ],
                      ),
                    ),
                  ),
                  Positioned(
                    top: 16.h,
                    right: 16.w,
                    child: InkWell(
                      onTap: () {
                        Get.back();
                      },
                      child: SvgPicture.asset(
                        SvgIcon.close,
                        height: 24.h,
                        width: 24.w,
                      ),
                    ),
                  ),
                ],
              ),
            ),
          );

          customSnackbar(
            "SUCCESS".tr,
            "YOUR_PAYMENT_HAS_BEEN_CONFIRMED".tr,
            AppColor.success,
          );
          Get.find<CartController>().cartItems.clear();
        } else if (isFailed || isCancel || isBack) {
          Get.back();
          customSnackbar("ERROR".tr, "PAYMENT_FAILED".tr, AppColor.error);
        }
      }
    });
  }
}
