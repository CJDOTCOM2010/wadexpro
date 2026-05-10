import 'dart:ui' as ui;
import 'package:flutter/foundation.dart';
import 'package:flutter/material.dart';
import 'package:google_maps_flutter/google_maps_flutter.dart';
import '../theme/app_colors.dart';

class MapMarkerGenerator {
  static Future<BitmapDescriptor> createCustomMarker({
    required Color color,
    required IconData icon,
    String? label,
  }) async {
    if (kIsWeb) {
      return BitmapDescriptor.defaultMarkerWithHue(
        color == Colors.blueAccent ? BitmapDescriptor.hueBlue : BitmapDescriptor.hueRed,
      );
    }
    
    final ui.PictureRecorder pictureRecorder = ui.PictureRecorder();
    final Canvas canvas = Canvas(pictureRecorder);
    const double size = 120;

    // 1. Draw the Shadow/Outer Glow
    final Paint glowPaint = Paint()
      ..color = color.withOpacity(0.3)
      ..maskFilter = const MaskFilter.blur(BlurStyle.normal, 10);
    canvas.drawCircle(const Offset(size / 2, size / 2), size / 2.5, glowPaint);

    // 2. Draw the Main Shield/Circle
    final Paint mainPaint = Paint()..color = color;
    canvas.drawCircle(const Offset(size / 2, size / 2), size / 3.5, mainPaint);

    // 3. Draw the Inner Border
    final Paint borderPaint = Paint()
      ..color = Colors.white
      ..style = PaintingStyle.stroke
      ..strokeWidth = 4;
    canvas.drawCircle(const Offset(size / 2, size / 2), size / 3.5, borderPaint);

    // 4. Draw the Icon
    final TextPainter textPainter = TextPainter(textDirection: TextDirection.ltr);
    textPainter.text = TextSpan(
      text: String.fromCharCode(icon.codePoint),
      style: TextStyle(
        fontSize: size / 4,
        fontFamily: icon.fontFamily,
        color: Colors.white,
      ),
    );
    textPainter.layout();
    textPainter.paint(
      canvas,
      Offset((size / 2) - (textPainter.width / 2), (size / 2) - (textPainter.height / 2)),
    );

    final ui.Image image = await pictureRecorder.endRecording().toImage(size.toInt(), size.toInt());
    final data = await image.toByteData(format: ui.ImageByteFormat.png);

    return BitmapDescriptor.fromBytes(data!.buffer.asUint8List());
  }

  static Future<BitmapDescriptor> createDriverMarker({required double heading}) async {
    if (kIsWeb) {
      return BitmapDescriptor.defaultMarkerWithHue(BitmapDescriptor.hueYellow);
    }

    final ui.PictureRecorder pictureRecorder = ui.PictureRecorder();
    final Canvas canvas = Canvas(pictureRecorder);
    const double size = 150;

    // 1. Draw the WADEXPRO Branded vehicle pulse
    final Paint pulsePaint = Paint()
      ..color = AppColors.accent.withOpacity(0.4)
      ..maskFilter = const MaskFilter.blur(BlurStyle.normal, 8);
    canvas.drawCircle(const Offset(size / 2, size / 2), size / 3, pulsePaint);

    // 2. Main Car Body (Simplified Premium Top-View)
    final Paint carPaint = Paint()..color = AppColors.primaryNavy;
    final rrect = RRect.fromRectAndRadius(
      Rect.fromCenter(center: const Offset(size / 2, size / 2), width: 40, height: 70),
      const Radius.circular(8),
    );
    canvas.drawRRect(rrect, carPaint);

    // 3. Car Details (Windshield/Lights)
    final Paint lightPaint = Paint()..color = AppColors.accent;
    canvas.drawCircle(const Offset(size / 2 - 12, size / 2 - 28), 5, lightPaint);
    canvas.drawCircle(const Offset(size / 2 + 12, size / 2 - 28), 5, lightPaint);

    final ui.Image image = await pictureRecorder.endRecording().toImage(size.toInt(), size.toInt());
    final data = await image.toByteData(format: ui.ImageByteFormat.png);

    return BitmapDescriptor.fromBytes(data!.buffer.asUint8List());
  }
}
