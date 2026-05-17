import 'package:flutter/material.dart';

class SplashConfig {
  final String? tagline;
  final int? durationMs;
  final String? bgColor;
  final String? secondaryColor;
  final bool? showRipple;
  final bool? showLogo;
  final bool? showBackground;
  final String? logoUrl;
  final String? logoMediaType;
  final String? backgroundUrl;
  final String? backgroundMediaType;

  SplashConfig({
    this.tagline,
    this.durationMs,
    this.bgColor,
    this.secondaryColor,
    this.showRipple,
    this.showLogo,
    this.showBackground,
    this.logoUrl,
    this.logoMediaType,
    this.backgroundUrl,
    this.backgroundMediaType,
  });

  static SplashConfig? fromJson(Map<String, dynamic>? json, String baseUrl) {
    if (json == null || json.isEmpty) return null;

    // Prepend server address if image paths start with /storage
    String? logo = json['logo_url'] as String?;
    if (logo != null && logo.startsWith('/storage')) {
      logo = '${baseUrl.replaceAll('/api/v1', '')}$logo?v=${DateTime.now().millisecondsSinceEpoch}';
    }

    String? background = json['background_url'] as String?;
    if (background != null && background.startsWith('/storage')) {
      background = '${baseUrl.replaceAll('/api/v1', '')}$background?v=${DateTime.now().millisecondsSinceEpoch}';
    }

    return SplashConfig(
      tagline: json['tagline'] as String?,
      durationMs: json['duration_ms'] as int?,
      bgColor: json['bg_color'] as String?,
      secondaryColor: json['secondary_color'] as String?,
      showRipple: json['show_ripple'] == true || json['show_ripple'] == 1,
      showLogo: json['show_logo'] == true || json['show_logo'] == 1,
      showBackground: json['show_background'] == true || json['show_background'] == 1,
      logoUrl: logo,
      logoMediaType: json['logo_media_type'] as String?,
      backgroundUrl: background,
      backgroundMediaType: json['background_media_type'] as String?,
    );
  }

  Color get backgroundColor => bgColor != null ? _colorFromHex(bgColor!) : const Color(0xFF000B1E);
  Color get accentColor => secondaryColor != null ? _colorFromHex(secondaryColor!) : const Color(0xFFFFC107);

  static Color _colorFromHex(String hexString) {
    final buffer = StringBuffer();
    if (hexString.length == 6 || hexString.length == 7) buffer.write('ff');
    buffer.write(hexString.replaceFirst('#', ''));
    try {
      return Color(int.parse(buffer.toString(), radix: 16));
    } catch (_) {
      return const Color(0xFF000B1E);
    }
  }
}
