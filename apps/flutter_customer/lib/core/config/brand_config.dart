import 'package:flutter/material.dart';

/// BrandConfig — Dynamic Branding Singleton
///
/// This class stores all brand identity values fetched from the Super Admin
/// dashboard via the /auth/config API. It replaces every hardcoded brand
/// reference across the app.
///
/// Usage:   BrandConfig.appName    → "WADEXPRO"
///          BrandConfig.shortName  → "WADEX"
///          BrandConfig.tagline    → "Move. Deliver. Thrive."
///          BrandConfig.primaryColor → Color(0xFF156400)
class BrandConfig {
  // --- Brand Text ---
  static String appName = 'WADEXPRO';
  static String shortName = 'WADEX';
  static String tagline = 'Move. Deliver. Thrive.';
  static String customerAppName = 'WADEXPRO';
  static String driverAppName = 'WADEXPRO Driver';
  static String supportEmail = 'ops@wadexpro.com';
  static String supportPhone = '';

  // --- Brand Assets ---
  static String? logoUrl;
  static String? appIconUrl;
  static String? splashBackgroundColor;

  // --- Brand Colors ---
  static Color primaryColor = const Color(0xFF156400);
  static Color accentColor = const Color(0xFFFFCC00);
  static Color darkColor = const Color(0xFF0A0A0A);
  static Color secondaryColor = const Color(0xFF0D4000);

  // --- Raw hex values (for serialization/debugging) ---
  static String _primaryHex = '#156400';
  static String _accentHex = '#FFCC00';
  static String _darkHex = '#0A0A0A';
  static String _secondaryHex = '#0D4000';

  /// Initialize branding from the API response's 'branding' block.
  /// Called during AppConfig.initialize() after fetching /auth/config.
  /// Falls back to hardcoded defaults if data is null or missing.
  static void fromJson(Map<String, dynamic>? data) {
    if (data == null) return;

    appName = data['brand_name'] ?? appName;
    shortName = data['brand_short_name'] ?? shortName;
    tagline = data['brand_tagline'] ?? tagline;
    logoUrl = data['brand_logo_url'];
    appIconUrl = data['customer_app_icon_url'] ?? data['app_icon_url'];
    customerAppName = data['customer_app_name'] ?? customerAppName;
    driverAppName = data['driver_app_name'] ?? driverAppName;
    supportEmail = data['support_email'] ?? supportEmail;
    supportPhone = data['support_phone'] ?? supportPhone;
    splashBackgroundColor = data['customer_splash_background'] ?? data['splash_background_color'];

    // Parse colors
    if (data['brand_primary_color'] != null) {
      _primaryHex = data['brand_primary_color'];
      primaryColor = _colorFromHex(_primaryHex);
    }
    if (data['brand_accent_color'] != null) {
      _accentHex = data['brand_accent_color'];
      accentColor = _colorFromHex(_accentHex);
    }
    if (data['brand_dark_color'] != null) {
      _darkHex = data['brand_dark_color'];
      darkColor = _colorFromHex(_darkHex);
    }
    if (data['brand_secondary_color'] != null) {
      _secondaryHex = data['brand_secondary_color'];
      secondaryColor = _colorFromHex(_secondaryHex);
    }

    debugPrint('BrandConfig: Loaded branding — $appName ($shortName) | Primary: $_primaryHex | Accent: $_accentHex');
  }

  /// Convert a hex color string (e.g. "#156400") to a Flutter Color.
  static Color _colorFromHex(String hexString) {
    final buffer = StringBuffer();
    if (hexString.length == 6 || hexString.length == 7) buffer.write('ff');
    buffer.write(hexString.replaceFirst('#', ''));
    try {
      return Color(int.parse(buffer.toString(), radix: 16));
    } catch (_) {
      return const Color(0xFF156400); // Fallback to default green
    }
  }
}
