import 'package:cached_network_image/cached_network_image.dart';
import 'package:flutter/material.dart';
import 'package:flutter_screenutil/flutter_screenutil.dart';
import 'package:get/get.dart';
import 'package:shopperz/app/modules/category/views/category_wise_product_screen.dart';
import 'package:shopperz/app/modules/home/controller/category_controller.dart';
import 'package:shopperz/widgets/textwidget.dart';
import '../../../../config/theme/app_color.dart';

class CategoryWidget extends StatefulWidget {
  const CategoryWidget({super.key});

  @override
  State<CategoryWidget> createState() => _CategoryWidgetState();
}

class _CategoryWidgetState extends State<CategoryWidget> {
  final categoryController = Get.find<CategoryController>();

  @override
  void initState() {
    categoryController.fetchCategory();
    super.initState();
  }

  @override
  Widget build(BuildContext context) {
    return Container(
      height: 120.h,
      width: double.infinity,
      color: AppColor.whiteColor,
      child: Obx(() {
        return ListView.builder(
          scrollDirection: Axis.horizontal,
          shrinkWrap: true,
          itemCount: categoryController.categoryModel.value.data!.length,
          itemBuilder: (context, index) {
            final category = categoryController.categoryModel.value.data!;
            return Center(
              child: Padding(
                padding: EdgeInsets.only(right: 12.w),
                child: InkWell(
                  onTap: () {
                    Get.to(
                      () => CategoryWiseProductScreen(
                        categoryModel: category[index],
                      ),
                    );
                  },
                  child: Container(
                    height: 85.h,
                    width: 72.w,
                    decoration: BoxDecoration(
                      color: AppColor.whiteColor,
                      borderRadius: BorderRadius.circular(8.r),
                      boxShadow: [
                        BoxShadow(
                          color: Colors.black.withOpacity(0.04),
                          offset: const Offset(0, 0),
                          blurRadius: 32.r,
                          spreadRadius: 0,
                        ),
                      ],
                    ),
                    child: Column(
                      children: [
                        CachedNetworkImage(
                          imageUrl: category[index].thumb.toString(),
                          imageBuilder:
                              (context, imageProvider) => Container(
                                height: 50.h,
                                width: double.infinity,
                                decoration: BoxDecoration(
                                  borderRadius: BorderRadius.only(
                                    topLeft: Radius.circular(8.r),
                                    topRight: Radius.circular(8.r),
                                  ),
                                  image: DecorationImage(
                                    image: imageProvider,
                                    fit: BoxFit.cover,
                                  ),
                                ),
                              ),
                        ),
                        Expanded(
                          child: Container(
                            height: 22.h,
                            width: double.infinity,
                            decoration: BoxDecoration(
                              color: AppColor.whiteColor,
                              borderRadius: BorderRadius.only(
                                bottomLeft: Radius.circular(8.r),
                                bottomRight: Radius.circular(8.r),
                              ),
                            ),
                            child: Center(
                              child: TextWidget(
                                text: category[index].name,
                                textAlign: TextAlign.center,
                                color: AppColor.titleTextColor,
                                fontSize: 12.sp,
                                fontWeight: FontWeight.w600,
                                maxLines: 2,
                                overflow: TextOverflow.ellipsis,
                              ),
                            ),
                          ),
                        ),
                      ],
                    ),
                  ),
                ),
              ),
            );
          },
        );
      }),
    );
  }
}
