// ignore_for_file: public_member_api_docs, sort_constructors_first
import 'package:flutter/material.dart';
import 'package:flutter_screenutil/flutter_screenutil.dart';
import 'package:get/get.dart';
import 'package:shopperz/app/modules/home/controller/promotion_controller.dart';
import 'package:shopperz/app/modules/promotion/views/promotion_wise_product_screen.dart';
import 'package:shopperz/widgets/shimmer/promotion_banner_shimmer.dart';

class MultiPromotionBanner extends StatelessWidget {
  const MultiPromotionBanner({
    super.key,
    this.image,
    this.width,
    this.pIndex,
  });

  final String? image;
  final double? width;
  final int? pIndex;
  @override
  Widget build(BuildContext context) {
    final promotionController = Get.put(PromotionalController());

    return Obx(() {
      return promotionController.loading.value
          ? SizedBox(
              height: 142.h,
              child: Padding(
                padding: EdgeInsets.only(top: 24.h),
                child: const PromotionBannerShimmer(),
              ),
            )
          : ListView.builder(
              shrinkWrap: true,
              padding: EdgeInsets.zero,
              scrollDirection: Axis.vertical,
              physics: const NeverScrollableScrollPhysics(),
              itemCount:
                  promotionController.multiPromotionModel.value.data!.length,
              itemBuilder: (context, index) {
                final promotion =
                    promotionController.multiPromotionModel.value.data;
                return pIndex == index
                    ? GestureDetector(
                        onTap: () {
                          Get.to(() => PromotionWiseProductScreen(
                              promotion: promotion[index]));
                        },
                        child: Column(
                          children: [
                            Container(
                              height: 142.h,
                              width: double.infinity,
                              decoration: BoxDecoration(
                                borderRadius: BorderRadius.circular(8.r),
                              ),
                              child: ClipRRect(
                                borderRadius: BorderRadius.circular(8.r),
                                child: Image.network(
                                  promotion![index].preview.toString(),
                                  fit: BoxFit.cover,
                                ),
                              ),
                            ),
                            SizedBox(height: 32.h),
                          ],
                        ),
                      )
                    : const SizedBox();
              },
            );
    });
  }
}
