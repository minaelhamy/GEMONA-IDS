// ignore_for_file: use_key_in_widget_constructors, must_be_immutable

import 'package:flutter/material.dart';
import 'package:flutter/services.dart';
import 'package:flutter_html/flutter_html.dart';
import 'package:flutter_screenutil/flutter_screenutil.dart';
import 'package:shopperz/widgets/appbar3.dart';
import 'package:webview_flutter/webview_flutter.dart';

import '../../../../config/theme/app_color.dart';

class PagesScreen extends StatefulWidget {
  final String? tittle;
  final String? description;
  final String? image;
  const PagesScreen({super.key, this.tittle, this.description, this.image});
  @override
  State<PagesScreen> createState() => _PagesScreenState();
}

class _PagesScreenState extends State<PagesScreen> {
  WebViewController controller = WebViewController();
  @override
  void initState() {
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
        backgroundColor: Colors.white,
        appBar: AppBarWidget3(text: widget.tittle),
        bottomNavigationBar: Container(
          height: 20.h,
          color: AppColor.whiteColor,
        ),
        body: SingleChildScrollView(
          child: Padding(
            padding: EdgeInsets.only(left: 16.w, right: 16.w),
            child: Column(
              children: [
                if (widget.image!.isNotEmpty)
                  Image.network(
                    widget.image!,
                    width: double.infinity,
                    fit: BoxFit.cover,
                  ),
                Html(data: widget.description),
              ],
            ),
          ),
        ),
      ),
    );
  }
}
