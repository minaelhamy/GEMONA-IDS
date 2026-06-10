import 'package:dio/dio.dart';
import 'package:shopperz/data/server/app_server.dart';
import 'package:shopperz/utils/api_list.dart';

import '../model/city_list_model.dart';
import '../model/country_list_model.dart';
import '../model/state_list_model.dart';

class AddressRepo {
  static Future<BaseOptions> getBasseOptions() async {
    BaseOptions options = BaseOptions(
      followRedirects: false,
      validateStatus: (status) {
        return status! < 500;
      },
      headers: AppServer.getAuthHeaders(),
    );

    return options;
  }

  static Future<CountryListModel> fetchCountries() async {
    Response? response;
    var dio = Dio(await getBasseOptions());
    String url = ApiList.countries.toString();
    try {
      response = await dio.get(url);
      if (response.statusCode == 200) {
        return CountryListModel.fromJson(response.data);
      }
    } catch (e) {
      rethrow;
    }
    return CountryListModel.fromJson(response.data);
  }

  static Future<StateListModel> fetchStates({
    required String countryName,
  }) async {
    Response? response;
    var dio = Dio(await getBasseOptions());
    String url = ApiList.states.toString() + countryName.toString();
    try {
      response = await dio.get(url);
      if (response.statusCode == 200) {
        return StateListModel.fromJson(response.data);
      }
    } catch (e) {
      print(e);
    }
    return StateListModel.fromJson(response?.data);
  }

  static Future<CityListModel> fetchCities({required String stateName}) async {
    Response? response;
    var dio = Dio(await getBasseOptions());
    String url = ApiList.cities.toString() + stateName.toString();
    try {
      response = await dio.get(url);
      if (response.statusCode == 200) {
        return CityListModel.fromJson(response.data);
      }
    } catch (e) {
      print(e);
    }
    return CityListModel.fromJson(response?.data);
  }
}
