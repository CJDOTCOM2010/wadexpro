import 'package:flutter/material.dart';

class AppColors {
  // WADEXPRO Green Palette
  static const Color primary = Color(0xFF156400); // WADEXPRO Green
  static const Color primaryNavy = Color(0xFF001229); // Deeper slate for drivers
  static const Color accent = Color(0xFFFFCC00);      // WADEX Gold
  static const Color eliteGold = Color(0xFFC0A060);   // Metallic Gold for Elite status
  static const Color background = Color(0xFFF8F9FA); // Clean off-white
  static const Color surface = Colors.white;
  static const Color error = Color(0xFFDC3545);
  static const Color textBody = Color(0xFF1E1E1E);
  static const Color textMuted = Color(0xFF6C757D);

  // Gradients for premium headers
  static const LinearGradient premiumGradient = LinearGradient(
    begin: Alignment.topLeft,
    end: Alignment.bottomRight,
    colors: [primary, primaryNavy],
  );
}
