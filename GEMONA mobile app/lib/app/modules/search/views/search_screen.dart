import 'package:flutter/material.dart';
import 'package:flutter/services.dart';
import 'package:flutter_screenutil/flutter_screenutil.dart';
import 'package:flutter_staggered_grid_view/flutter_staggered_grid_view.dart';
import 'package:flutter_svg/svg.dart';
import 'package:get/get.dart';
import 'package:shopperz/app/modules/auth/views/sign_in.dart';
import 'package:shopperz/app/modules/category/views/category_wise_product_screen.dart';
import 'package:shopperz/app/modules/search/controller/search_controller.dart';
import 'package:shopperz/app/modules/wishlist/controller/wishlist_controller.dart';
import 'package:shopperz/main.dart';
import 'package:shopperz/utils/images.dart';
import 'package:shopperz/utils/svg_icon.dart';
import 'package:shopperz/widgets/appbar3.dart';
import '../../../../config/theme/app_color.dart';
import '../../../../widgets/textwidget.dart';
import '../../product/widgets/product.dart';
import '../../product_details/views/product_details.dart';

class SearchScreen extends StatefulWidget {
  const SearchScreen({super.key});

  @override
  State<SearchScreen> createState() => _SearchScreenState();
}

class _SearchScreenState extends State<SearchScreen> {
  final searchController = Get.put(ProductSearchController());
  final wishlistController = Get.find<WishlistController>();

  bool isCross = false;

  @override
  void initState() {
    searchController.serchProduct(name: '');
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
        resizeToAvoidBottomInset: false,
        appBar: AppBarWidget3(text: "Search".tr),
        body: Column(
          children: [
            SizedBox(height: 4.h),
            Container(
              height: 42.h,
              width: 328.w,
              decoration: BoxDecoration(
                borderRadius: BorderRadius.circular(21.r),
                color: Colors.green,
              ),
              child: TextField(
                controller: searchController.searchTextController,
                showCursor: true,
                readOnly: false,
                autofocus: true,
                cursorColor: AppColor.textColor,
                onChanged: (search) {
                  searchController.serchProduct(name: search);
                  setState(() {
                    search.isNotEmpty ? isCross = true : isCross = false;
                  });
                },
                onSubmitted: (value) {
                  Get.to(() => const CategoryWiseProductScreen());
                },
                decoration: InputDecoration(
                  contentPadding: EdgeInsets.symmetric(
                    horizontal: 0.w,
                    vertical: 0.h,
                  ),
                  hintText: "Search".tr,
                  hintStyle: const TextStyle(color: Colors.grey),
                  prefixIcon: InkWell(
                    onTap: () {
                      searchController.serchProduct(
                        name: searchController.searchTextController.text,
                      );
                    },
                    child: Padding(
                      padding: EdgeInsets.all(10.r),
                      child: SvgPicture.asset(
                        SvgIcon.search,
                        colorFilter: const ColorFilter.mode(
                          Colors.grey,
                          BlendMode.dst,
                        ),
                      ),
                    ),
                  ),
                  suffixIcon:
                      isCross == false
                          ? const SizedBox()
                          : SizedBox(
                            child: Padding(
                              padding: EdgeInsets.all(12.r),
                              child: InkWell(
                                onTap: () {
                                  searchController.searchTextController.clear();
                                  setState(() {
                                    isCross = false;
                                  });
                                  searchController.serchProduct(
                                    name:
                                        searchController
                                            .searchTextController
                                            .text,
                                  );
                                },
                                child: const Icon(
                                  Icons.close,
                                  color: Colors.black,
                                ),
                              ),
                            ),
                          ),
                  filled: true,
                  fillColor: AppColor.borderColor,
                  focusedBorder: OutlineInputBorder(
                    borderRadius: BorderRadius.all(Radius.circular(12.r)),
                    borderSide: BorderSide(
                      color: AppColor.borderColor,
                      width: 1.w,
                    ),
                  ),
                  enabledBorder: OutlineInputBorder(
                    borderRadius: BorderRadius.all(Radius.circular(12.r)),
                    borderSide: BorderSide(
                      width: 0.w,
                      color: AppColor.borderColor,
                    ),
                  ),
                ),
              ),
            ),
            SizedBox(height: 4.h),
            Expanded(
              child: Obx(() {
                if (searchController.searchResult.isEmpty) {
                  return Padding(
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
                            text: 'NO_PRODUCT_FOUND'.tr,
                            textAlign: TextAlign.center,
                            color: AppColor.textColor,
                            fontSize: 14.sp,
                            fontWeight: FontWeight.w500,
                          ),
                        ],
                      ),
                    ),
                  );
                }
                return SingleChildScrollView(
                  child: Padding(
                    padding: EdgeInsets.symmetric(
                      horizontal: 16.w,
                      vertical: 16.h,
                    ),
                    child: Obx(
                      () => StaggeredGrid.count(
                        crossAxisCount: 2,
                        mainAxisSpacing: 16.h,
                        crossAxisSpacing: 16.w,
                        children: [
                          for (
                            var index = 0;
                            index < searchController.searchResult.length;
                            index++
                          )
                            ProductWidget(
                              onTap: () {
                                Get.to(
                                  () => ProductDetailsScreen(
                                    categoryWiseProduct:
                                        searchController.searchResult[index],
                                  ),
                                );
                              },
                              wishlist:
                                  wishlistController.favList.contains(
                                            searchController
                                                .searchResult[index]
                                                .id!,
                                          ) ||
                                          searchController
                                                  .searchResult[index]
                                                  .wishlist ==
                                              true
                                      ? true
                                      : false,
                              favTap: () async {
                                if (box.read('isLogedIn') != false && box.read('isLogedIn') != null) {
                                  if (searchController
                                          .searchResult[index]
                                          .wishlist ==
                                      true) {
                                    await wishlistController
                                        .toggleFavoriteFalse(
                                          searchController
                                              .searchResult[index]
                                              .id!,
                                        );

                                    wishlistController.showFavorite(
                                      searchController.searchResult[index].id!,
                                    );
                                  }
                                  if (searchController
                                          .searchResult[index]
                                          .wishlist ==
                                      false) {
                                    await wishlistController.toggleFavoriteTrue(
                                      searchController.searchResult[index].id!,
                                    );

                                    wishlistController.showFavorite(
                                      searchController.searchResult[index].id!,
                                    );
                                  }
                                } else {
                                  Get.to(() => const SignInScreen());
                                }
                              },
                              productImage:
                                  searchController.searchResult[index].cover!,
                              title: searchController.searchResult[index].name,
                              rating:
                                  searchController
                                      .searchResult[index]
                                      .ratingStar,
                              currentPrice:
                                  searchController
                                      .searchResult[index]
                                      .currencyPrice,
                              discountPrice:
                                  searchController
                                      .searchResult[index]
                                      .discountedPrice,
                              textRating:
                                  searchController
                                      .searchResult[index]
                                      .ratingStarCount,
                              flashSale:
                                  searchController
                                      .searchResult[index]
                                      .flashSale ??
                                  false,
                              isOffer:
                                  searchController.searchResult[index].isOffer!,
                            ),
                          SizedBox(height: 12.h),
                        ],
                      ),
                    ),
                  ),
                );
              }),
            ),
          ],
        ),
      ),
    );
  }
}
