import 'package:dio/dio.dart';
import 'package:google_maps_flutter/google_maps_flutter.dart';
import '../../../core/network/api_client.dart';

class RideRepository {
  final ApiClient _apiClient;

  RideRepository(this._apiClient);

  /// Fetch fare estimations for different vehicle types.
  Future<List<dynamic>> getFareEstimations({
    required LatLng pickup,
    required LatLng dropoff,
    String? promoCode,
  }) async {
    try {
      // Fetch multiple estimations (simulated here since the controller currently returns singular)
      // For production, the API should return a list for the selector.
      // We will loop through WADEXP's supported categories.
      final List<String> categories = ['moto', 'economy', 'comfort', 'xl'];
      final List<dynamic> results = [];

      for (var type in categories) {
        final response = await _apiClient.instance.post('/v1/logistics/rides/estimate', data: {
          'pickup_lat': pickup.latitude,
          'pickup_lng': pickup.longitude,
          'dropoff_lat': dropoff.latitude,
          'dropoff_lng': dropoff.longitude,
          'vehicle_type': type,
          'promo_code': promoCode,
        });
        results.add(response.data);
      }
      
      return results;
    } on DioException catch (e) {
      throw e.response?.data['message'] ?? 'Failed to calculate fares.';
    }
  }

  /// Create a new ride request.
  Future<Map<String, dynamic>> createRideRequest({
    required LatLng pickup,
    required LatLng dropoff,
    required String pickupAddress,
    required String dropoffAddress,
    required String vehicleType,
    String? promoCode,
  }) async {
    try {
      final response = await _apiClient.instance.post('/v1/logistics/rides', data: {
        'pickup_lat': pickup.latitude,
        'pickup_lng': pickup.longitude,
        'pickup_address': pickupAddress,
        'destination_lat': dropoff.latitude,
        'destination_lng': dropoff.longitude,
        'destination_address': dropoffAddress,
        'vehicle_type': vehicleType,
        'promo_code': promoCode,
      });
      return response.data['data'];
    } on DioException catch (e) {
      throw e.response?.data['message'] ?? 'Failed to request ride.';
    }
  }

  /// Fetch any active ride for the user.
  Future<Map<String, dynamic>?> getActiveRide() async {
    try {
      final response = await _apiClient.instance.get('/v1/logistics/rides/active');
      return response.data['data'];
    } on DioException catch (e) {
      if (e.response?.statusCode == 404) return null;
      throw e.response?.data['message'] ?? 'Failed to fetch active ride.';
    }
  }

  /// Trigger SOS alert.
  Future<void> triggerSOS({
    required double lat,
    required double lng,
    required String? rideId,
  }) async {
    try {
      await _apiClient.instance.post('/v1/logistics/rides/sos', data: {
        'lat': lat,
        'lng': lng,
        'ride_id': rideId,
      });
    } on DioException catch (e) {
      throw e.response?.data['message'] ?? 'Failed to trigger SOS.';
    }
  }

  /// Create a new delivery order.
  Future<Map<String, dynamic>> createOrder({
    required String pickupAddress,
    required double pickupLat,
    required double pickupLng,
    required String contactName,
    required String contactPhone,
    required String packageDescription,
    required List<Map<String, dynamic>> stops,
    String priority = 'express',
  }) async {
    try {
      final response = await _apiClient.instance.post('/v1/logistics/orders', data: {
        'pickup_address': pickupAddress,
        'pickup_lat': pickupLat,
        'pickup_lng': pickupLng,
        'contact_name': contactName,
        'contact_phone': contactPhone,
        'package_description': packageDescription,
        'stops': stops,
        'priority': priority,
      });
      return response.data['data'];
    } on DioException catch (e) {
      throw e.response?.data['message'] ?? 'Failed to create delivery order.';
    }
  }
}
