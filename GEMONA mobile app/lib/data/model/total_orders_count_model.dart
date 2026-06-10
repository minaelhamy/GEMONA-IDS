class TotalOrdersCount {
  Data? data;

  TotalOrdersCount({this.data});

  TotalOrdersCount.fromJson(Map<String, dynamic> json) {
    data = json['data'] != null ? Data.fromJson(json['data']) : null;
  }

  Map<String, dynamic> toJson() {
    final Map<String, dynamic> data = <String, dynamic>{};
    if (this.data != null) {
      data['data'] = this.data!.toJson();
    }
    return data;
  }
}

class Data {
  int? totalOrders;

  Data({this.totalOrders});

  Data.fromJson(Map<String, dynamic> json) {
    totalOrders = json['total_orders'];
  }

  Map<String, dynamic> toJson() {
    final Map<String, dynamic> data = <String, dynamic>{};
    data['total_orders'] = totalOrders;
    return data;
  }
}