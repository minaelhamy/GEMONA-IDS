class TotalCompleteOrdersCount {
  Data? data;

  TotalCompleteOrdersCount({this.data});

  TotalCompleteOrdersCount.fromJson(Map<String, dynamic> json) {
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
  int? totalCompletedOrders;

  Data({this.totalCompletedOrders});

  Data.fromJson(Map<String, dynamic> json) {
    totalCompletedOrders = json['total_completed_orders'];
  }

  Map<String, dynamic> toJson() {
    final Map<String, dynamic> data = <String, dynamic>{};
    data['total_completed_orders'] = totalCompletedOrders;
    return data;
  }
}