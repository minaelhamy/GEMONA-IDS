import 'package:flutter/material.dart';
import 'package:flutter/services.dart';
import 'package:flutter_screenutil/flutter_screenutil.dart';
import 'package:get/get.dart';
import 'package:shopperz/app/modules/category/controller/category_tree_controller.dart';
import 'package:shopperz/app/modules/category/views/category_wise_product_screen.dart';
import 'package:shopperz/app/modules/category/widgets/category_widget.dart';
import 'package:shopperz/app/modules/sub_category/views/sub_category_screen.dart';
import 'package:shopperz/utils/images.dart';
import 'package:shopperz/widgets/loader/loader.dart';

import '../../../../config/theme/app_color.dart';
import '../../../../widgets/appbar.dart';
import '../../../../widgets/textwidget.dart';

class CategoryScreen extends StatefulWidget {
  const CategoryScreen({super.key});

  @override
  State<CategoryScreen> createState() => _CategoryScreenState();
}

class _CategoryScreenState extends State<CategoryScreen> {
  final categoryTreeController = Get.put(CategoryTreeController());

  @override
  void initState() {
    categoryTreeController.fetchCategoryTree();
    super.initState();
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
        appBar: PreferredSize(
          preferredSize: Size.fromHeight(48.h),
          child: const AppBarWidget(isSearch: false),
        ),
        body: Obx(
          () => Stack(
            alignment: Alignment.center,
            children: [
              RefreshIndicator(
                onRefresh: () async {
                  categoryTreeController.fetchCategoryTree();
                },
                color: AppColor.primaryColor,
                child: SingleChildScrollView(
                  physics: AlwaysScrollableScrollPhysics(),
                  child: Padding(
                    padding: EdgeInsets.only(
                      top: 16.h,
                      left: 16.w,
                      right: 16.w,
                    ),
                    child: Obx(() {
                      return categoryTreeController.categoryTreeList.isEmpty
                          ? const SizedBox()
                          : categoryTreeController.categoryTreeList.isEmpty
                          ? Center(
                            child: Padding(
                              padding: EdgeInsets.only(top: 100.h),
                              child: Center(
                                child: Column(
                                  children: [
                                    Image.asset(
                                      AppImages.emptyIcon,
                                      height: 300.h,
                                      width: 300.w,
                                    ),
                                    SizedBox(height: 16.h),
                                    TextWidget(
                                      text: 'NO_SUB_CATEGORY_FOUND'.tr,
                                      textAlign: TextAlign.center,
                                      color: AppColor.textColor,
                                      fontSize: 14.sp,
                                      fontWeight: FontWeight.w500,
                                    ),
                                  ],
                                ),
                              ),
                            ),
                          )
                          : ListView.builder(
                            itemCount:
                                categoryTreeController.categoryTreeList.length,
                            shrinkWrap: true,
                            physics: const NeverScrollableScrollPhysics(),
                            itemBuilder: (context, index) {
                              final categoryTree =
                                  categoryTreeController.categoryTreeList;
                              return CategoryList(
                                text: categoryTree[index].name.toString(),
                                onTapProduct: () {
                                  Get.to(
                                    () => CategoryWiseProductScreen(
                                      categoryTreeModel: categoryTree[index],
                                    ),
                                    transition: Transition.fade,
                                  );
                                },
                                onTapSubCategory: () {
                                  Get.to(
                                    () => SubCategoryScreen(
                                      categoryTreeModel: categoryTree[index],
                                    ),
                                    transition: Transition.fade,
                                  );
                                },
                              );
                            },
                          );
                    }),
                  ),
                ),
              ),
              categoryTreeController.isLoading.value
                  ? const Center(child: LoaderCircle())
                  : const SizedBox(),
            ],
          ),
        ),
      ),
    );
  }
}
