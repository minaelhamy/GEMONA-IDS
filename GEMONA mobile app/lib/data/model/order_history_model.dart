class OrderHistoryModel {
  List<Data>? data;
  Links? links;
  Meta? meta;

  OrderHistoryModel({this.data, this.links, this.meta});

  OrderHistoryModel.fromJson(Map<String, dynamic> json) {
    if (json['data'] != null) {
      data = <Data>[];
      json['data'].forEach((v) {
        data!.add(Data.fromJson(v));
      });
    }
    links = json['links'] != null ? Links.fromJson(json['links']) : null;
    meta = json['meta'] != null ? Meta.fromJson(json['meta']) : null;
  }

  Map<String, dynamic> toJson() {
    final Map<String, dynamic> data = <String, dynamic>{};
    if (this.data != null) {
      data['data'] = this.data!.map((v) => v.toJson()).toList();
    }
    if (links != null) {
      data['links'] = links!.toJson();
    }
    if (meta != null) {
      data['meta'] = meta!.toJson();
    }
    return data;
  }
}

class Data {
  int? id;
  String? orderSerialNo;
  int? userId;
  String? totalAmountPrice;
  String? totalCurrencyPrice;
  int? paymentStatus;
  int? status;
  String? statusName;
  int? orderItems;
  String? orderDatetime;
  User? user;

  Data(
      {this.id,
      this.orderSerialNo,
      this.userId,
      this.totalAmountPrice,
      this.totalCurrencyPrice,
      this.paymentStatus,
      this.status,
      this.statusName,
      this.orderItems,
      this.orderDatetime,
      this.user});

  Data.fromJson(Map<String, dynamic> json) {
    id = json['id'];
    orderSerialNo = json['order_serial_no'];
    userId = json['user_id'];
    totalAmountPrice = json['total_amount_price'];
    totalCurrencyPrice = json['total_currency_price'];
    paymentStatus = json['payment_status'];
    status = json['status'];
    statusName = json['status_name'];
    orderItems = json['order_items'];
    orderDatetime = json['order_datetime'];
    user = json['user'] != null ? User.fromJson(json['user']) : null;
  }

  Map<String, dynamic> toJson() {
    final Map<String, dynamic> data = <String, dynamic>{};
    data['id'] = id;
    data['order_serial_no'] = orderSerialNo;
    data['user_id'] = userId;
    data['total_amount_price'] = totalAmountPrice;
    data['total_currency_price'] = totalCurrencyPrice;
    data['payment_status'] = paymentStatus;
    data['status'] = status;
    data['status_name'] = statusName;
    data['order_items'] = orderItems;
    data['order_datetime'] = orderDatetime;
    if (user != null) {
      data['user'] = user!.toJson();
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

class Links {
  String? first;
  String? last;
  String? prev;
  String? next;

  Links({this.first, this.last, this.prev, this.next});

  Links.fromJson(Map<String, dynamic> json) {
    first = json['first'];
    last = json['last'];
    prev = json['prev'];
    next = json['next'];
  }

  Map<String, dynamic> toJson() {
    final Map<String, dynamic> data = <String, dynamic>{};
    data['first'] = first;
    data['last'] = last;
    data['prev'] = prev;
    data['next'] = next;
    return data;
  }
}

class Meta {
  int? currentPage;
  int? from;
  int? lastPage;
  String? path;
  int? perPage;
  int? to;
  int? total;

  Meta(
      {this.currentPage,
      this.from,
      this.lastPage,
      this.path,
      this.perPage,
      this.to,
      this.total});

  Meta.fromJson(Map<String, dynamic> json) {
    currentPage = json['current_page'];
    from = json['from'];
    lastPage = json['last_page'];
    path = json['path'];
    perPage = json['per_page'];
    to = json['to'];
    total = json['total'];
  }

  Map<String, dynamic> toJson() {
    final Map<String, dynamic> data = <String, dynamic>{};
    data['current_page'] = currentPage;
    data['from'] = from;
    data['last_page'] = lastPage;
    data['path'] = path;
    data['per_page'] = perPage;
    data['to'] = to;
    data['total'] = total;
    return data;
  }
}
