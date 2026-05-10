import 'package:flutter/material.dart';

class AppColors {
  // Primary Palette
  static const Color primary = Color(0xFF156400); // WADEXPRO Green
  static const Color accent = Color(0xFF0D4000);  // Dark Green
  static const Color secondary = Color(0xFF0A3000); 

  // Compatibility Aliases for Tactical UI
  static const Color primaryNavy = Color(0xFF001C46); 
  static const Color secondaryGold = Color(0xFFFFCC00); 
  static const Color eliteGold = Color(0xFFFFA000);

  // Neutral Palette
  static const Color background = Color(0xFFF8F9FA);
  static const Color surface = Colors.white;
  static const Color textBody = Color(0xFF1E1E1E);
  static const Color textMuted = Color(0xFF6C757D);
  
  // Feedback
  static const Color success = Color(0xFF28A745);
  static const Color error = Color(0xFFDC3545);
  static const Color warning = Color(0xFFFFC107);

  // Gradients
  static const LinearGradient luxuryGradient = LinearGradient(
    colors: [primary, Color(0xFF003070)],
    begin: Alignment.topLeft,
    end: Alignment.bottomRight,
  );
}
