import 'package:flutter/material.dart';

class OnboardingPageConfig {
  final String title;
  final String description;
  final String imagePath;
  final String bgColorHex;
  final String textColorHex;
  final String buttonColorHex;
  final String buttonText;
  final String buttonType;
  final String mediaType; // 'image' or 'video'
  final String layoutStyle;
  final bool isNetworkImage;

  const OnboardingPageConfig({
    required this.title,
    required this.description,
    required this.imagePath,
    this.mediaType = 'image',
    this.layoutStyle = 'full_bleed',
    this.bgColorHex = '#000B1E',
    this.textColorHex = '#FFFFFF',
    this.buttonColorHex = '#FFB800',
    this.buttonText = 'Next',
    this.buttonType = 'action_below_text',
    this.isNetworkImage = false,
  });

  factory OnboardingPageConfig.fromJson(Map<String, dynamic> json, String baseUrl) {
    String imageUrl = json['image_url'] ?? '';
    if (imageUrl.startsWith('/storage')) {
      imageUrl = '${baseUrl.replaceAll('/api/v1', '')}$imageUrl?v=${DateTime.now().millisecondsSinceEpoch}';
    }

    return OnboardingPageConfig(
      title: json['title'] ?? '',
      description: json['description'] ?? '',
      imagePath: imageUrl,
      mediaType: json['media_type'] ?? 'image',
      bgColorHex: json['bg_color'] ?? '#000B1E',
      textColorHex: json['text_color'] ?? '#FFFFFF',
      buttonColorHex: json['button_color'] ?? '#FFB800',
      buttonText: json['button_text'] ?? 'Next',
      buttonType: json['button_type'] ?? 'action_below_text',
      layoutStyle: json['layout_style'] ?? 'full_bleed',
      isNetworkImage: true,
    );
  }

  Color get backgroundColor => _colorFromHex(bgColorHex);
  Color get textColor => _colorFromHex(textColorHex);
  Color get buttonColor => _colorFromHex(buttonColorHex);

  static Color _colorFromHex(String hexString) {
    final buffer = StringBuffer();
    if (hexString.length == 6 || hexString.length == 7) buffer.write('ff');
    buffer.write(hexString.replaceFirst('#', ''));
    return Color(int.parse(buffer.toString(), radix: 16));
  }
}

class OnboardingConfig {
  final List<OnboardingPageConfig> pages;
  final String skipText;
  final String getStartedText;

  const OnboardingConfig({
    required this.pages,
    this.skipText = 'Skip',
    this.getStartedText = 'Get Started',
  });
}
