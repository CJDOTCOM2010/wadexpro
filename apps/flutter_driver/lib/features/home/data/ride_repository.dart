import 'package:dio/dio.dart';
import '../../../core/network/api_client.dart';

class RideRepository {
  final ApiClient _apiClient;

  RideRepository(this._apiClient);

  /// Update current location (Telemetry).
  Future<void> updateLocation(double lat, double lng) async {
    try {
      await _apiClient.instance.post('/v1/logistics/driver/location', data: {
        'lat': lat,
        'lng': lng,
      });
    } on DioException catch (e) {
      // Background location failures are usually silent to avoid UX disruption
      print('Telemetry Sync Failed: ${e.message}');
    }
  }

  /// Accept an available ride.
  Future<void> acceptRide(String rideId) async {
    try {
      await _apiClient.instance.patch('/v1/logistics/driver/rides/$rideId/accept');
    } on DioException catch (e) {
      throw e.response?.data['message'] ?? 'Failed to accept ride.';
    }
  }

  /// Update ride status (en_route, arrived, in_progress, etc).
  Future<void> updateRideStatus(String rideId, String status) async {
    try {
      await _apiClient.instance.patch('/v1/logistics/driver/rides/$rideId/status', data: {
        'status': status,
      });
    } on DioException catch (e) {
      throw e.response?.data['message'] ?? 'Failed to update ride status.';
    }
  }

  /// Trigger an SOS emergency alert.
  Future<void> triggerSOS({
    required double lat,
    required double lng,
    String? rideId,
  }) async {
    try {
      await _apiClient.instance.post('/v1/logistics/sos', data: {
        'lat': lat,
        'lng': lng,
        'ride_request_id': rideId,
      });
    } on DioException catch (e) {
      throw e.response?.data['message'] ?? 'Failed to send SOS alert.';
    }
  }

  /// Update the status of a specific stop in a multi-stop order.
  Future<void> updateStopStatus(String stopId, String status, {String? notes}) async {
    try {
      await _apiClient.instance.patch('/v1/logistics/driver/stops/$stopId/status', data: {
        'status': status,
        'notes': notes,
      });
    } on DioException catch (e) {
      throw e.response?.data['message'] ?? 'Failed to update stop status.';
    }
  }
}
