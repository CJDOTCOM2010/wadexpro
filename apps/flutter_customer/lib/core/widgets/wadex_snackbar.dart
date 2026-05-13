import 'package:flutter/material.dart';
import 'package:google_fonts/google_fonts.dart';
import '../theme/app_colors.dart';

enum SnackbarType { success, error, warning, info }

class WadexSnackBar {
  static void show(
    BuildContext context, {
    required String message,
    SnackbarType type = SnackbarType.info,
    String? title,
    Duration duration = const Duration(seconds: 4),
  }) {
    if (!context.mounted) return;

    ScaffoldMessenger.of(context).hideCurrentSnackBar();

    Color backgroundColor;
    Color iconColor;
    IconData icon;

    switch (type) {
      case SnackbarType.success:
        backgroundColor = const Color(0xFF1E4620); // Darker green
        iconColor = AppColors.success;
        icon = Icons.check_circle_outline_rounded;
        break;
      case SnackbarType.error:
        backgroundColor = const Color(0xFF4A0E13); // Darker red
        iconColor = AppColors.error;
        icon = Icons.error_outline_rounded;
        break;
      case SnackbarType.warning:
        backgroundColor = const Color(0xFF5A4308); // Darker yellow/gold
        iconColor = AppColors.warning;
        icon = Icons.warning_amber_rounded;
        break;
      case SnackbarType.info:
      default:
        backgroundColor = const Color(0xFF182A4A); // Darker blue
        iconColor = Colors.blueAccent;
        icon = Icons.info_outline_rounded;
        break;
    }

    final snackBar = SnackBar(
      elevation: 0,
      behavior: SnackBarBehavior.floating,
      backgroundColor: Colors.transparent,
      padding: EdgeInsets.zero,
      duration: duration,
      content: Container(
        margin: const EdgeInsets.symmetric(horizontal: 16, vertical: 8),
        padding: const EdgeInsets.symmetric(horizontal: 16, vertical: 14),
        decoration: BoxDecoration(
          color: backgroundColor,
          borderRadius: BorderRadius.circular(16),
          border: Border.all(color: iconColor.withOpacity(0.3), width: 1.5),
          boxShadow: [
            BoxShadow(
              color: backgroundColor.withOpacity(0.5),
              blurRadius: 10,
              offset: const Offset(0, 4),
            ),
          ],
        ),
        child: Row(
          crossAxisAlignment: CrossAxisAlignment.start,
          children: [
            Container(
              padding: const EdgeInsets.all(8),
              decoration: BoxDecoration(
                color: iconColor.withOpacity(0.15),
                shape: BoxShape.circle,
              ),
              child: Icon(icon, color: iconColor, size: 24),
            ),
            const SizedBox(width: 16),
            Expanded(
              child: Column(
                crossAxisAlignment: CrossAxisAlignment.start,
                mainAxisSize: MainAxisSize.min,
                children: [
                  if (title != null) ...[
                    Text(
                      title,
                      style: GoogleFonts.outfit(
                        fontWeight: FontWeight.bold,
                        fontSize: 16,
                        color: Colors.white,
                      ),
                    ),
                    const SizedBox(height: 4),
                  ],
                  Text(
                    message,
                    style: GoogleFonts.outfit(
                      fontSize: 14,
                      color: Colors.white.withOpacity(0.9),
                      height: 1.4,
                    ),
                  ),
                ],
              ),
            ),
          ],
        ),
      ),
    );

    ScaffoldMessenger.of(context).showSnackBar(snackBar);
  }

  // Helper methods for quick usage
  static void showSuccess(BuildContext context, String message, {String? title}) {
    show(context, message: message, type: SnackbarType.success, title: title ?? 'Success');
  }

  static void showError(BuildContext context, String message, {String? title}) {
    show(context, message: message, type: SnackbarType.error, title: title ?? 'Error');
  }

  static void showWarning(BuildContext context, String message, {String? title}) {
    show(context, message: message, type: SnackbarType.warning, title: title ?? 'Warning');
  }

  static void showInfo(BuildContext context, String message, {String? title}) {
    show(context, message: message, type: SnackbarType.info, title: title ?? 'Notice');
  }
}
