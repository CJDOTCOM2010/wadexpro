import 'dart:async';
import 'dart:math';
import 'package:flutter_riverpod/flutter_riverpod.dart';
import 'package:dio/dio.dart';

class TrackingService {
  final Dio _dio;
  Timer? _timer;
  
  // Starting point: Accra, Ghana
  double _currentLat = 5.6037;
  double _currentLng = -0.1870;

  TrackingService(this._dio);

  void startTracking() {
    _timer?.cancel();
    _timer = Timer.periodic(const Duration(seconds: 10), (timer) {
      _simulateMovement();
      _pushLocation();
    });
  }

  void stopTracking() {
    _timer?.cancel();
  }

  void _simulateMovement() {
    // Basic jitter simulation for development
    final random = Random();
    _currentLat += (random.nextDouble() - 0.5) * 0.001;
    _currentLng += (random.nextDouble() - 0.5) * 0.001;
  }

  Future<void> _pushLocation() async {
    try {
      // In a real app, we'd use the driver's auth token
      await _dio.post('/logistics/driver/location', data: {
        'lat': _currentLat,
        'lng': _currentLng,
        'speed_kmh': 40.0,
        'bearing': 120.0,
      });
      print('Location pushed: $_currentLat, $_currentLng');
    } catch (e) {
      print('Failed to push location: $e');
    }
  }
}

final dioProvider = Provider((ref) => Dio(BaseOptions(
  baseUrl: 'http://localhost:8000/api/v1',
  headers: {'Accept': 'application/json'},
)));

final trackingServiceProvider = Provider((ref) {
  final dio = ref.watch(dioProvider);
  return TrackingService(dio);
});
