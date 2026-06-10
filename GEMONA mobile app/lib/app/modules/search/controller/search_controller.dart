import 'package:flutter/material.dart';
import 'package:get/get.dart';
import 'package:shopperz/app/modules/search/model/all_product.dart';
import 'package:shopperz/data/remote_services/remote_services.dart';
import '../../category/model/category_wise_product.dart';

class ProductSearchController extends GetxController {
  final searchTextController = TextEditingController();
  final productList = <Datum>[].obs;
  var searchResult = <Product>[].obs;
  final isLoading = false.obs;

  Future<dynamic> serchProduct({required String? name}) async {
    isLoading(true);
    final result = await RemoteServices().searchProduct(name: name ?? '');
    isLoading(false);
    return result.fold(
      (error) {
        isLoading(false);
      },
      (result) {
        searchResult.value = result.data!.products ?? [];
      },
    );
  }
}
