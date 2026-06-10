import 'package:flutter/material.dart';
import 'package:flutter_screenutil/flutter_screenutil.dart';
import 'package:flutter_svg/svg.dart';
import 'package:fluttertoast/fluttertoast.dart';
import 'package:get/get.dart';
import 'package:google_fonts/google_fonts.dart';
import 'package:shopperz/app/modules/auth/controller/auth_controler.dart';
import 'package:shopperz/app/modules/auth/views/sign_in.dart';
import 'package:shopperz/app/modules/profile/controller/profile_controller.dart';
import 'package:shopperz/app/modules/shipping/controller/address_controller.dart';
import 'package:shopperz/app/modules/shipping/controller/show_address_controller.dart';
import 'package:shopperz/main.dart';
import 'package:shopperz/utils/constant.dart';
import 'package:shopperz/utils/svg_icon.dart';
import 'package:shopperz/utils/validation_rules.dart';
import 'package:shopperz/widgets/loader/loader.dart';
import '../../../../config/theme/app_color.dart';
import '../../../../widgets/custom_form_field.dart';
import '../../../../widgets/custom_phone_form_field.dart';
import '../../../../widgets/custom_text.dart';
import '../../../../widgets/form_field_title.dart';
import '../../../../widgets/secondary_button2.dart';

class AddNewAddressDialog extends StatefulWidget {
  const AddNewAddressDialog({super.key});

  @override
  State<AddNewAddressDialog> createState() => _AddNewAddressDialogState();
}

class _AddNewAddressDialogState extends State<AddNewAddressDialog> {
  AuthController auth = Get.put(AuthController());
  AddressController addressController = Get.put(AddressController());
  final showAddressController = Get.put(ShowAddressController());
  ProfileController profile = Get.put(ProfileController());

  @override
  void initState() {
    super.initState();
    auth.getSetting();
    auth.getCountryCode();
    addressController.nameTextController.clear();
    addressController.phoneTextController.clear();
    addressController.countryController.clear();
    addressController.stateController.clear();
    addressController.cityController.clear();
    addressController.emailTextController.clear();
    addressController.zipTextController.clear();
    addressController.streetTextController.clear();
    addressController.countryCode = auth.countryCode;
  }

  @override
  void dispose() {
    addressController.stateListMap.clear();
    addressController.cityListMap.clear();
    super.dispose();
  }

  @override
  Widget build(BuildContext context) {
    return Stack(
      alignment: Alignment.center,
      children: [
        Container(
          decoration: BoxDecoration(
            borderRadius: BorderRadius.circular(12.r),
            color: Colors.green,
          ),
          width: 328.w,
          child: Material(
            borderRadius: BorderRadius.circular(12.r),
            child: Form(
              key: addressController.formkey,
              child: SingleChildScrollView(
                child: Padding(
                  padding: EdgeInsets.symmetric(horizontal: 16.w),
                  child: SingleChildScrollView(
                    child: Column(
                      crossAxisAlignment: CrossAxisAlignment.start,
                      children: [
                        SizedBox(height: 16.h),
                        CustomText(
                          text: "Add New Address".tr,
                          size: 22.sp,
                          weight: FontWeight.w700,
                        ),
                        SizedBox(height: 10.h),
                        FormFieldTitle(title: "Full Name".tr),
                        SizedBox(height: 4.h),
                        CustomFormField(
                          controller: addressController.nameTextController,
                          validator: (name) => ValidationRules().fullname(name),
                        ),
                        SizedBox(height: 10.h),
                        FormFieldTitle(title: "Email".tr),
                        SizedBox(height: 4.h),
                        CustomFormField(
                          controller: addressController.emailTextController,
                          validator: (value) {
                            RegExp regExp = RegExp(regularExpressionEmail);
                            if (value!.isNotEmpty) {
                              if (!regExp.hasMatch(value)) {
                                return 'Enter Valid Email Address'.tr;
                              }
                            }
                            return null;
                          },
                        ),
                        SizedBox(height: 10.h),
                        FormFieldTitle(title: "Phone".tr),
                        SizedBox(height: 4.h),
                        CustomPhoneFormField(
                          phoneController:
                              addressController.phoneTextController,
                          validator: (phone) => ValidationRules().phone(phone),
                          prefix: Padding(
                            padding: EdgeInsets.only(left: 10.w),
                            child: PopupMenuButton(
                              constraints: BoxConstraints(maxHeight: 300.h),
                              shape: RoundedRectangleBorder(
                                borderRadius: BorderRadius.all(
                                  Radius.circular(10.r),
                                ),
                              ),
                              position: PopupMenuPosition.under,
                              itemBuilder: (ctx) => List.generate(
                                auth.countryCodeModel!.data!.length,
                                (index) => PopupMenuItem(
                                  height: 32.h,
                                  onTap: () async {
                                    setState(() {
                                      addressController.countryCode = auth
                                          .countryCodeModel!
                                          .data![index]
                                          .callingCode
                                          .toString();
                                    });
                                  },
                                  child: Text(
                                    auth
                                        .countryCodeModel!
                                        .data![index]
                                        .callingCode
                                        .toString(),
                                    style: GoogleFonts.urbanist(
                                      color: AppColor.textColor,
                                      fontWeight: FontWeight.w500,
                                      fontSize: 16.sp,
                                    ),
                                  ),
                                ),
                              ),
                              child: Row(
                                children: [
                                  Text(
                                    addressController.countryCode,
                                    style: GoogleFonts.urbanist(
                                      color: AppColor.textColor,
                                      fontSize: 16.sp,
                                      fontWeight: FontWeight.w500,
                                    ),
                                  ),
                                  SizedBox(width: 5.w),
                                  SvgPicture.asset(SvgIcon.down),
                                ],
                              ),
                            ),
                          ),
                        ),
                        SizedBox(height: 10.h),
                        FormFieldTitle(title: "COUNTRY".tr),
                        SizedBox(height: 4.h),
                        Obx(
                          () => addressController.countryListMap.isNotEmpty
                              ? PopupMenuButton(
                                  constraints: BoxConstraints(maxHeight: 300.h),
                                  shape: RoundedRectangleBorder(
                                    borderRadius: BorderRadius.all(
                                      Radius.circular(10.r),
                                    ),
                                  ),
                                  position: PopupMenuPosition.under,
                                  itemBuilder: (ctx) => List.generate(
                                    addressController
                                        .countryListModel!
                                        .data!
                                        .length,
                                    (index) => PopupMenuItem(
                                      height: 32.h,
                                      onTap: () async {
                                        setState(() {
                                          addressController.stateController
                                              .clear();
                                          addressController.cityController
                                              .clear();
                                          addressController.stateListMap
                                              .clear();
                                          addressController.cityListMap.clear();
                                          addressController
                                              .countryController
                                              .text = addressController
                                              .countryListModel!
                                              .data![index]
                                              .name
                                              .toString();

                                          addressController.fetchStates(
                                            countryName: addressController
                                                .countryController
                                                .text,
                                          );
                                        });
                                      },
                                      child: Text(
                                        addressController
                                            .countryListModel!
                                            .data![index]
                                            .name
                                            .toString(),
                                        style: GoogleFonts.urbanist(
                                          color: AppColor.textColor,
                                          fontWeight: FontWeight.w500,
                                          fontSize: 16.sp,
                                        ),
                                      ),
                                    ),
                                  ),
                                  child: IgnorePointer(
                                    child: CustomFormField(
                                      controller:
                                          addressController.countryController,
                                      validator: (country) =>
                                          ValidationRules().country(country),
                                      suffixIcon: Padding(
                                        padding: EdgeInsets.all(12.r),
                                        child: SvgPicture.asset(SvgIcon.down),
                                      ),
                                      isSuffixIcon: true,
                                      enabled: false,
                                    ),
                                  ),
                                )
                              : CustomFormField(
                                  controller:
                                      addressController.countryController,
                                  validator: (country) =>
                                      ValidationRules().country(country),
                                  suffixIcon: Padding(
                                    padding: EdgeInsets.all(12.r),
                                    child: SvgPicture.asset(SvgIcon.down),
                                  ),
                                  isSuffixIcon: true,
                                  enabled: false,
                                ),
                        ),
                        SizedBox(height: 10.h),
                        FormFieldTitle(title: "STATE".tr),
                        SizedBox(height: 4.h),
                        Obx(
                          () => addressController.stateListMap.isNotEmpty
                              ? PopupMenuButton(
                                  constraints: BoxConstraints(maxHeight: 300.h),
                                  shape: RoundedRectangleBorder(
                                    borderRadius: BorderRadius.all(
                                      Radius.circular(10.r),
                                    ),
                                  ),
                                  position: PopupMenuPosition.under,
                                  itemBuilder: (ctx) => List.generate(
                                    addressController
                                        .stateListModel!
                                        .data!
                                        .length,
                                    (index) => PopupMenuItem(
                                      height: 32.h,
                                      onTap: () async {
                                        setState(() {
                                          addressController.cityController
                                              .clear();
                                          addressController.cityListMap.clear();
                                          addressController
                                              .stateController
                                              .text = addressController
                                              .stateListModel!
                                              .data![index]
                                              .name
                                              .toString();

                                          addressController.fetchCities(
                                            stateName: addressController
                                                .stateController
                                                .text,
                                          );
                                        });
                                      },
                                      child: Text(
                                        addressController
                                            .stateListModel!
                                            .data![index]
                                            .name
                                            .toString(),
                                        style: GoogleFonts.urbanist(
                                          color: AppColor.textColor,
                                          fontWeight: FontWeight.w500,
                                          fontSize: 16.sp,
                                        ),
                                      ),
                                    ),
                                  ),
                                  child: IgnorePointer(
                                    child: CustomFormField(
                                      controller:
                                          addressController.stateController,
                                      suffixIcon: Padding(
                                        padding: EdgeInsets.all(12.r),
                                        child: SvgPicture.asset(SvgIcon.down),
                                      ),
                                      isSuffixIcon: true,
                                      enabled: false,
                                    ),
                                  ),
                                )
                              : CustomFormField(
                                  controller: addressController.stateController,
                                  suffixIcon: Padding(
                                    padding: EdgeInsets.all(12.r),
                                    child: SvgPicture.asset(SvgIcon.down),
                                  ),
                                  isSuffixIcon: true,
                                  enabled: false,
                                ),
                        ),
                        SizedBox(height: 10.h),
                        FormFieldTitle(title: "CITY".tr),
                        SizedBox(height: 4.h),
                        Obx(
                          () => addressController.cityListMap.isNotEmpty
                              ? PopupMenuButton(
                                  constraints: BoxConstraints(maxHeight: 300.h),
                                  shape: RoundedRectangleBorder(
                                    borderRadius: BorderRadius.all(
                                      Radius.circular(10.r),
                                    ),
                                  ),
                                  position: PopupMenuPosition.under,
                                  itemBuilder: (ctx) => List.generate(
                                    addressController
                                        .cityListModel!
                                        .data!
                                        .length,
                                    (index) => PopupMenuItem(
                                      height: 32.h,
                                      onTap: () async {
                                        setState(() {
                                          addressController
                                              .cityController
                                              .text = addressController
                                              .cityListModel!
                                              .data![index]
                                              .name
                                              .toString();
                                        });
                                      },
                                      child: Text(
                                        addressController
                                            .cityListModel!
                                            .data![index]
                                            .name
                                            .toString(),
                                        style: GoogleFonts.urbanist(
                                          color: AppColor.textColor,
                                          fontWeight: FontWeight.w500,
                                          fontSize: 16.sp,
                                        ),
                                      ),
                                    ),
                                  ),
                                  child: IgnorePointer(
                                    child: CustomFormField(
                                      controller:
                                          addressController.cityController,
                                      suffixIcon: Padding(
                                        padding: EdgeInsets.all(12.r),
                                        child: SvgPicture.asset(SvgIcon.down),
                                      ),
                                      isSuffixIcon: true,
                                      enabled: false,
                                    ),
                                  ),
                                )
                              : CustomFormField(
                                  controller: addressController.cityController,
                                  suffixIcon: Padding(
                                    padding: EdgeInsets.all(12.r),
                                    child: SvgPicture.asset(SvgIcon.down),
                                  ),
                                  isSuffixIcon: true,
                                  enabled: false,
                                ),
                        ),
                        SizedBox(height: 10.h),
                        FormFieldTitle(title: "Zip Code".tr),
                        SizedBox(height: 4.h),
                        CustomFormField(
                          controller: addressController.zipTextController,
                        ),
                        SizedBox(height: 10.h),
                        FormFieldTitle(title: "Street Address".tr),
                        SizedBox(height: 4.h),
                        CustomFormField(
                          controller: addressController.streetTextController,
                          validator: (address) =>
                              ValidationRules().address(address),
                        ),
                        SizedBox(height: 24.h),
                        Row(
                          mainAxisAlignment: MainAxisAlignment.spaceBetween,
                          children: [
                            SecondaryButton2(
                              height: 48.h,
                              width: 158.w,
                              text: "Add Address".tr,
                              buttonColor: AppColor.primaryColor,
                              textColor: AppColor.whiteColor,
                              onTap: () async {
                                if (addressController
                                    .countryController
                                    .text
                                    .isEmpty) {
                                  Fluttertoast.showToast(
                                    msg: "COUNTRY_IS_REQUIRED".tr,
                                    toastLength: Toast.LENGTH_SHORT,
                                    gravity: ToastGravity.CENTER,
                                    timeInSecForIosWeb: 1,
                                    backgroundColor: Colors.red,
                                    textColor: Colors.white,
                                    fontSize: 16.0,
                                  );
                                } else if (addressController
                                    .stateController
                                    .text
                                    .isEmpty) {
                                  Fluttertoast.showToast(
                                    msg: "STATE_IS_REQUIRED".tr,
                                    toastLength: Toast.LENGTH_SHORT,
                                    gravity: ToastGravity.CENTER,
                                    timeInSecForIosWeb: 1,
                                    backgroundColor: Colors.red,
                                    textColor: Colors.white,
                                    fontSize: 16.0,
                                  );
                                } else if (addressController
                                    .cityController
                                    .text
                                    .isEmpty) {
                                  Fluttertoast.showToast(
                                    msg: "CITY_IS_REQUIRED".tr,
                                    toastLength: Toast.LENGTH_SHORT,
                                    gravity: ToastGravity.CENTER,
                                    timeInSecForIosWeb: 1,
                                    backgroundColor: Colors.red,
                                    textColor: Colors.white,
                                    fontSize: 16.0,
                                  );
                                } else if (addressController
                                    .formkey
                                    .currentState!
                                    .validate()) {
                                  if (box.read("token") != null) {
                                    await addressController.submitAddress(
                                      fullName: addressController
                                          .nameTextController
                                          .text,
                                      email: addressController
                                          .emailTextController
                                          .text,
                                      phone: addressController
                                          .phoneTextController
                                          .text,
                                      countryCode:
                                          addressController.countryCode,
                                      country: addressController
                                          .countryController
                                          .text,
                                      streetAddress: addressController
                                          .streetTextController
                                          .text,
                                      state: addressController
                                          .stateController
                                          .text,
                                      city:
                                          addressController.cityController.text,
                                      zip: addressController
                                          .zipTextController
                                          .text,
                                    );
                                    await showAddressController.showAdresses();
                                    profile.getAddress();
                                  } else {
                                    Get.off(() => const SignInScreen());
                                  }
                                }
                              },
                            ),
                            SecondaryButton2(
                              height: 48.h,
                              width: 114.w,
                              text: "Cancel".tr,
                              buttonColor: AppColor.cartColor,
                              textColor: AppColor.textColor,
                              onTap: () {
                                Get.back();
                              },
                            ),
                          ],
                        ),
                        SizedBox(height: 20.h),
                      ],
                    ),
                  ),
                ),
              ),
            ),
          ),
        ),
        Obx(
          () => addressController.isLoading.value ? LoaderCircle() : SizedBox(),
        ),
      ],
    );
  }
}
