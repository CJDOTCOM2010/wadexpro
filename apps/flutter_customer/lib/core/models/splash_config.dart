import 'package:flutter/material.dart';

class SplashConfig {
  final String tagline;
  final int durationMs;
  final String bgColor;
  final String secondaryColor;
  final bool showRipple;
  final bool showLogo;
  final bool showBackground;
  final String? logoUrl;
  final String logoMediaType;
  final String? backgroundUrl;
  final String backgroundMediaType;

  SplashConfig({
    required this.tagline,
    required this.durationMs,
    required this.bgColor,
    required this.secondaryColor,
    required this.showRipple,
    required this.showLogo,
    required this.showBackground,
    this.logoUrl,
    this.logoMediaType = 'image',
    this.backgroundUrl,
    this.backgroundMediaType = 'image',
  });

  factory SplashConfig.fromJson(Map<String, dynamic> json, String baseUrl) {
    // Prepend server address if image paths start with /storage
    String? logo = json['logo_url'];
    if (logo != null && logo.startsWith('/storage')) {
      logo = '${baseUrl.replaceAll('/api/v1', '')}$logo?v=${DateTime.now().millisecondsSinceEpoch}';
    }

    String? background = json['background_url'];
    if (background != null && background.startsWith('/storage')) {
      background = '${baseUrl.replaceAll('/api/v1', '')}$background?v=${DateTime.now().millisecondsSinceEpoch}';
    }

    return SplashConfig(
      tagline: json['tagline'] ?? 'Move. Deliver. Thrive.',
      durationMs: json['duration_ms'] ?? 3000,
      bgColor: json['bg_color'] ?? '#000B1E',
      secondaryColor: json['secondary_color'] ?? '#FFC107',
      showRipple: json['show_ripple'] == true || json['show_ripple'] == 1,
      showLogo: json['show_logo'] == true || json['show_logo'] == 1,
      showBackground: json['show_background'] == true || json['show_background'] == 1,
      logoUrl: logo,
      logoMediaType: json['logo_media_type'] ?? 'image',
      backgroundUrl: background,
      backgroundMediaType: json['background_media_type'] ?? 'image',
    );
  }

  Color get backgroundColor => _colorFromHex(bgColor);
  Color get accentColor => _colorFromHex(secondaryColor);

  static Color _colorFromHex(String hexString) {
    final buffer = StringBuffer();
    if (hexString.length == 6 || hexString.length == 7) buffer.write('ff');
    buffer.write(hexString.replaceFirst('#', ''));
    return Color(int.parse(buffer.toString(), radix: 16));
  }
}
