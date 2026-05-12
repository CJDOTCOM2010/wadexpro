import 'package:google_maps_flutter/google_maps_flutter.dart';

class PolylineGenerator {
  /// Generates a straight line of points between two LatLngs for mock demonstration.
  /// In a production environment with a valid Google Maps API Key, you would use
  /// `flutter_polyline_points` to fetch the actual road-following polyline points.
  static Future<List<LatLng>> getMockRoute(LatLng start, LatLng end) async {
    // Simulate network delay for fetching directions
    await Future.delayed(const Duration(milliseconds: 600));

    final int numPoints = 20;
    List<LatLng> points = [];
    
    for (int i = 0; i <= numPoints; i++) {
      double fraction = i / numPoints;
      double lat = start.latitude + (end.latitude - start.latitude) * fraction;
      double lng = start.longitude + (end.longitude - start.longitude) * fraction;
      
      // Add slight jitter/bezier effect for visual simulation of a road curve
      if (i > 0 && i < numPoints) {
        lat += 0.001 * (i % 2 == 0 ? 1 : -1);
        lng += 0.001 * (i % 3 == 0 ? 1 : -1);
      }
      
      points.add(LatLng(lat, lng));
    }
    
    return points;
  }
}
