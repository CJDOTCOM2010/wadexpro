import '../../../../core/network/api_client.dart';

class LogisticsRepository {
  final ApiClient _apiClient;

  LogisticsRepository(this._apiClient);

  /// Update the status of a specific stop in a multi-stop order.
  Future<Map<String, dynamic>> updateStopStatus(String id, String status, {String? notes, String? podPhoto}) async {
    final response = await _apiClient.instance.patch('/v1/logistics/driver/stops/$id/status', data: {
      'status': status,
      'notes': notes,
      'pod_photo': podPhoto,
    });
    return response.data['data'];
  }

  /// Update the overall status of an order.
  Future<Map<String, dynamic>> updateOrderStatus(String id, String status) async {
    final response = await _apiClient.instance.patch('/v1/logistics/driver/orders/$id/status', data: {
      'status': status,
    });
    return response.data['data'];
  }
}
