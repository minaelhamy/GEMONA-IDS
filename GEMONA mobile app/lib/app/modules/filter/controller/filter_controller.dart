import 'dart:convert';
import 'package:get/get.dart';

class FilterController extends GetxController {
  final selectedOption = "".obs;
  final selectedBrandIndex = 0.obs;
  final brandIndexList = <String>[];

  final variationIndexList = <String>[];
  final variationObjectList = <dynamic>[];
  String? encodeVaritionObject;

  String? brands;
  String? homeBrands;

  void addBrandId(String id) {
    if (brandIndexList.contains(id)) {
      brandIndexList.remove(id);
    } else {
      brandIndexList.add(id);
    }

    brands = brandIndexList.toString();
    homeBrands = brandIndexList.toString();
  }

  addVariationId(String productOptionId) {
    if (variationIndexList.contains(productOptionId)) {
      variationIndexList.remove(productOptionId);
    } else {
      variationIndexList.add(productOptionId);
    }
  }

  void addVariationObject(variationObject) {
    final int? incomingAttribute = int.tryParse(
      variationObject['attribute'].toString(),
    );
    final int? incomingOption = int.tryParse(
      variationObject['option'].toString(),
    );

    bool exists = variationObjectList.any(
      (item) =>
          item['attribute'] is int &&
          item['option'] is int &&
          item['attribute'] == incomingAttribute &&
          item['option'] == incomingOption,
    );

    if (exists) {
      variationObjectList.removeWhere(
        (item) =>
            item['attribute'] is int &&
            item['option'] is int &&
            item['attribute'] == incomingAttribute &&
            item['option'] == incomingOption,
      );
      encodeVaritionObject = jsonEncode(variationObjectList).toString();
    } else {
      variationObjectList.add({
        'attribute': incomingAttribute,
        'option': incomingOption,
      });
      encodeVaritionObject = jsonEncode(variationObjectList).toString();
    }
  }

  resetFilter() {
    selectedOption.value = "";
    homeBrands = "";
    brands = "";
    encodeVaritionObject = "";
    variationObjectList.clear();
    variationIndexList.clear();
    brandIndexList.clear();
    selectedBrandIndex.value = -1;
  }
}
