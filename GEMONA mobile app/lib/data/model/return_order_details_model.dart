class ReturnOrdersDetailsModel {
  Data? data;

  ReturnOrdersDetailsModel({this.data});

  ReturnOrdersDetailsModel.fromJson(Map<String, dynamic> json) {
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
  int? id;
  int? returnReasonId;
  String? returnReasonName;
  String? note;
  String? rejectReason;
  int? orderId;
  String? orderDatetime;
  String? returnDatetime;
  String? returnTotalCurrencyPrice;
  String? returnTotalPrice;
  int? userId;
  int? orderSerialNo;
  int? status;
  List<String>? images;
  User? user;
  List<ReturnProducts>? returnProducts;

  Data(
      {this.id,
      this.returnReasonId,
      this.returnReasonName,
      this.note,
      this.rejectReason,
      this.orderId,
      this.orderDatetime,
      this.returnDatetime,
      this.returnTotalCurrencyPrice,
      this.returnTotalPrice,
      this.userId,
      this.orderSerialNo,
      this.status,
      this.images,
      this.user,
      this.returnProducts});

  Data.fromJson(Map<String, dynamic> json) {
    id = json['id'];
    returnReasonId = json['return_reason_id'];
    returnReasonName = json['return_reason_name'];
    note = json['note'];
    rejectReason = json['reject_reason'];
    orderId = json['order_id'];
    orderDatetime = json['order_datetime'];
    returnDatetime = json['return_datetime'];
    returnTotalCurrencyPrice = json['return_total_currency_price'];
    returnTotalPrice = json['return_total_price'];
    userId = json['user_id'];
    orderSerialNo = json['order_serial_no'];
    status = json['status'];
    images = json['images'].cast<String>();
    user = json['user'] != null ? User.fromJson(json['user']) : null;
    if (json['return_products'] != null) {
      returnProducts = <ReturnProducts>[];
      json['return_products'].forEach((v) {
        returnProducts!.add(ReturnProducts.fromJson(v));
      });
    }
  }

  Map<String, dynamic> toJson() {
    final Map<String, dynamic> data = <String, dynamic>{};
    data['id'] = id;
    data['return_reason_id'] = returnReasonId;
    data['return_reason_name'] = returnReasonName;
    data['note'] = note;
    data['reject_reason'] = rejectReason;
    data['order_id'] = orderId;
    data['order_datetime'] = orderDatetime;
    data['return_datetime'] = returnDatetime;
    data['return_total_currency_price'] = returnTotalCurrencyPrice;
    data['return_total_price'] = returnTotalPrice;
    data['user_id'] = userId;
    data['order_serial_no'] = orderSerialNo;
    data['status'] = status;
    data['images'] = images;
    if (user != null) {
      data['user'] = user!.toJson();
    }
    if (returnProducts != null) {
      data['return_products'] =
          returnProducts!.map((v) => v.toJson()).toList();
    }
    return data;
  }
}

class User {
  int? id;
  String? name;
  String? phone;
  String? email;
  String? username;
  String? balance;
  String? currencyBalance;
  String? image;
  int? roleId;
  String? countryCode;
  int? order;
  String? createDate;
  String? updateDate;

  User(
      {this.id,
      this.name,
      this.phone,
      this.email,
      this.username,
      this.balance,
      this.currencyBalance,
      this.image,
      this.roleId,
      this.countryCode,
      this.order,
      this.createDate,
      this.updateDate});

  User.fromJson(Map<String, dynamic> json) {
    id = json['id'];
    name = json['name'];
    phone = json['phone'];
    email = json['email'];
    username = json['username'];
    balance = json['balance'];
    currencyBalance = json['currency_balance'];
    image = json['image'];
    roleId = json['role_id'];
    countryCode = json['country_code'];
    order = json['order'];
    createDate = json['create_date'];
    updateDate = json['update_date'];
  }

  Map<String, dynamic> toJson() {
    final Map<String, dynamic> data = <String, dynamic>{};
    data['id'] = id;
    data['name'] = name;
    data['phone'] = phone;
    data['email'] = email;
    data['username'] = username;
    data['balance'] = balance;
    data['currency_balance'] = currencyBalance;
    data['image'] = image;
    data['role_id'] = roleId;
    data['country_code'] = countryCode;
    data['order'] = order;
    data['create_date'] = createDate;
    data['update_date'] = updateDate;
    return data;
  }
}

class ReturnProducts {
  int? id;
  int? productId;
  String? productName;
  String? productImage;
  String? variationNames;
  String? categoryName;
  int? quantity;
  String? price;
  String? currencyPrice;
  String? totalCurrencyPrice;
  String? returnCurrencyPrice;

  ReturnProducts(
      {this.id,
      this.productId,
      this.productName,
      this.productImage,
      this.variationNames,
      this.categoryName,
      this.quantity,
      this.price,
      this.currencyPrice,
      this.totalCurrencyPrice,
      this.returnCurrencyPrice});

  ReturnProducts.fromJson(Map<String, dynamic> json) {
    id = json['id'];
    productId = json['product_id'];
    productName = json['product_name'];
    productImage = json['product_image'];
    variationNames = json['variation_names'];
    categoryName = json['category_name'];
    quantity = json['quantity'];
    price = json['price'];
    currencyPrice = json['currency_price'];
    totalCurrencyPrice = json['total_currency_price'];
    returnCurrencyPrice = json['return_currency_price'];
  }

  Map<String, dynamic> toJson() {
    final Map<String, dynamic> data = <String, dynamic>{};
    data['id'] = id;
    data['product_id'] = productId;
    data['product_name'] = productName;
    data['product_image'] = productImage;
    data['variation_names'] = variationNames;
    data['category_name'] = categoryName;
    data['quantity'] = quantity;
    data['price'] = price;
    data['currency_price'] = currencyPrice;
    data['total_currency_price'] = totalCurrencyPrice;
    data['return_currency_price'] = returnCurrencyPrice;
    return data;
  }
}