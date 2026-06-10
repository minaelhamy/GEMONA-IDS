import 'package:flutter/material.dart';
import 'package:flutter/services.dart';
import 'package:flutter_screenutil/flutter_screenutil.dart';
import 'package:flutter_svg/flutter_svg.dart';
import 'package:get/get.dart';
import 'package:shopperz/app/modules/category/model/category_wise_product.dart';
import 'package:shopperz/app/modules/filter/widgets/brands_widget.dart';
import 'package:shopperz/app/modules/filter/widgets/sort_by_widget.dart';
import 'package:shopperz/utils/svg_icon.dart';
import 'package:shopperz/widgets/custom_text.dart';
import 'package:shopperz/widgets/primary_button.dart';
import 'package:shopperz/widgets/textwidget.dart';
import '../../../../config/theme/app_color.dart';
import '../../../../widgets/devider.dart';
import '../../category/controller/category_wise_product_controller.dart';
import '../controller/filter_controller.dart';

class FilterScreen extends StatefulWidget {
  const FilterScreen({super.key, this.cateWiseProductModel});
  final Data? cateWiseProductModel;
  @override
  State<FilterScreen> createState() => _FilterScreenState();
}

class _FilterScreenState extends State<FilterScreen> {
  final cateWiseProductController = Get.find<CategoryWiseProductController>();
  final filterController = Get.find<FilterController>();
  var isExpanded = <int>{};

  @override
  Widget build(BuildContext context) {
    final attributeKey =
        cateWiseProductController.variationsMap?.keys.toList() ?? [];

    return AnnotatedRegion<SystemUiOverlayStyle>(
      value: const SystemUiOverlayStyle(
        systemNavigationBarColor: Colors.white,
        systemNavigationBarIconBrightness: Brightness.dark,
        statusBarIconBrightness: Brightness.dark,
        statusBarColor: Colors.transparent,
        statusBarBrightness: Brightness.dark,
      ),
      child: Scaffold(
        appBar: AppBar(
          backgroundColor: AppColor.primaryBackgroundColor,
          elevation: 0,
          toolbarHeight: 48.h,
          leadingWidth: double.infinity,
          leading: Padding(
            padding: EdgeInsets.only(left: 16.w, top: 10.h, bottom: 10.h),
            child: TextWidget(
              text: 'Filter & Sorting'.tr,
              textAlign: TextAlign.left,
              color: AppColor.textColor,
              fontSize: 18.sp,
              fontWeight: FontWeight.w700,
            ),
          ),
        ),
        bottomNavigationBar: Padding(
          padding: EdgeInsets.only(left: 16.w, right: 16.w, bottom: 20.h),
          child: SizedBox(
            height: 40.h,
            child: Row(
              mainAxisAlignment: MainAxisAlignment.spaceBetween,
              children: [
                InkWell(
                  onTap: () {
                    Get.back();
                    filterController.resetFilter();
                  },
                  child: Material(
                    child: Ink(
                      height: 48.h,
                      width: 150,
                      decoration: BoxDecoration(
                        color: AppColor.grayColor,
                        borderRadius: BorderRadius.circular(24.r),
                      ),
                      child: Center(
                        child: CustomText(
                          text: 'Cancel'.tr,
                          color: Colors.black,
                          weight: FontWeight.w700,
                          size: 16.sp,
                        ),
                      ),
                    ),
                  ),
                ),
                PrimaryButton(
                  onTap: () {
                    Get.back();
                  },
                  width: 150.w,
                  text: 'Apply'.tr,
                ),
              ],
            ),
          ),
        ),
        body: SingleChildScrollView(
          child: Padding(
            padding: EdgeInsets.only(left: 16.w, top: 16.h, right: 16.w),
            child: Column(
              children: [
                const SortByWidget(),
                const DeviderWidget(),
                widget.cateWiseProductModel?.brands?.isNotEmpty ?? false
                    ? BrandListWidget(
                      cateWiseProduct: widget.cateWiseProductModel,
                    )
                    : SizedBox(),
                ListView.builder(
                  shrinkWrap: true,
                  physics: const NeverScrollableScrollPhysics(),
                  itemCount: attributeKey.length,
                  itemBuilder: (context, index) {
                    final attributeName = attributeKey[index];

                    final attributeOptions =
                        cateWiseProductController.variationsMap![attributeName]
                            as List<dynamic>;

                    return Theme(
                      data: ThemeData().copyWith(
                        dividerColor: Colors.transparent,
                      ),
                      child: ExpansionTile(
                        childrenPadding: EdgeInsets.zero,
                        tilePadding: EdgeInsets.zero,
                        onExpansionChanged: (expanded) {
                          setState(() {
                            if (expanded) {
                              isExpanded.add(index);
                            } else {
                              isExpanded.remove(index);
                            }
                          });
                        },
                        title: Column(
                          crossAxisAlignment: CrossAxisAlignment.start,
                          children: [
                            CustomText(
                              text:
                                  attributeOptions.first['attribute_name']
                                      .toString(),
                              color: AppColor.textColor,
                              size: 18.sp,
                              weight: FontWeight.w600,
                            ),
                            SizedBox(height: 16.h),
                            const DeviderWidget(),
                          ],
                        ),
                        trailing: SvgPicture.asset(
                          isExpanded.contains(index)
                              ? SvgIcon.up
                              : SvgIcon.downEx,
                          height: 20.h,
                          width: 20.w,
                        ),
                        children:
                            attributeOptions.map<Widget>((value) {
                              return Padding(
                                padding: EdgeInsets.only(bottom: 16.h),
                                child: Row(
                                  children: [
                                    InkWell(
                                      onTap: () {
                                        setState(() {});
                                        var variationObject = {
                                          "attribute":
                                              value["product_attribute_id"],
                                          "option":
                                              value["product_attribute_option_id"],
                                        };
                                        filterController.addVariationId(
                                          value["product_attribute_option_id"]
                                              .toString(),
                                        );
                                        filterController.addVariationObject(
                                          variationObject,
                                        );
                                      },
                                      child: SvgPicture.asset(
                                        filterController.variationIndexList
                                                .contains(
                                                  value["product_attribute_option_id"]
                                                      .toString(),
                                                )
                                            ? SvgIcon.checkActive
                                            : SvgIcon.check,
                                        height: 20.h,
                                        width: 20.w,
                                      ),
                                    ),
                                    SizedBox(width: 12.w),
                                    TextWidget(
                                      text:
                                          value['attribute_option_name']
                                              .toString(),
                                      color: AppColor.textColor,
                                      fontSize: 14.sp,
                                      fontWeight: FontWeight.w500,
                                    ),
                                  ],
                                ),
                              );
                            }).toList(),
                      ),
                    );
                  },
                ),
              ],
            ),
          ),
        ),
      ),
    );
  }
}
