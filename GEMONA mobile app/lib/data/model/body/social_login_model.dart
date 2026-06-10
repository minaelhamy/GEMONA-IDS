// To parse this JSON data, do
//
//     final socialLoginModel = socialLoginModelFromJson(jsonString);

import 'dart:convert';

SocialLoginModel socialLoginModelFromJson(String str) =>
    SocialLoginModel.fromJson(json.decode(str));

String socialLoginModelToJson(SocialLoginModel data) =>
    json.encode(data.toJson());

class SocialLoginModel {
  final List<Datum>? data;

  SocialLoginModel({this.data});

  factory SocialLoginModel.fromJson(Map<String, dynamic> json) =>
      SocialLoginModel(
        data: json["data"] == null
            ? []
            : List<Datum>.from(json["data"]!.map((x) => Datum.fromJson(x))),
      );

  Map<String, dynamic> toJson() => {
    "data": data == null
        ? []
        : List<dynamic>.from(data!.map((x) => x.toJson())),
  };
}

class Datum {
  final int? id;
  final String? name;
  final String? slug;
  final int? status;
  final List<Option>? options;

  Datum({this.id, this.name, this.slug, this.status, this.options});

  factory Datum.fromJson(Map<String, dynamic> json) => Datum(
    id: json["id"],
    name: json["name"],
    slug: json["slug"],
    status: json["status"],
    options: json["options"] == null
        ? []
        : List<Option>.from(json["options"]!.map((x) => Option.fromJson(x))),
  );

  Map<String, dynamic> toJson() => {
    "id": id,
    "name": name,
    "slug": slug,
    "status": status,
    "options": options == null
        ? []
        : List<dynamic>.from(options!.map((x) => x.toJson())),
  };
}

class Option {
  final int? id;
  final String? option;
  final String? value;
  final int? type;
  final dynamic activities;

  Option({this.id, this.option, this.value, this.type, this.activities});

  factory Option.fromJson(Map<String, dynamic> json) => Option(
    id: json["id"],
    option: json["option"],
    value: json["value"],
    type: json["type"],
    activities: json["activities"],
  );

  Map<String, dynamic> toJson() => {
    "id": id,
    "option": option,
    "value": value,
    "type": type,
    "activities": activities,
  };
}
