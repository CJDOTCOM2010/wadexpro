import 'package:dio/dio.dart';
import '../../../core/network/api_client.dart';

class ChatRepository {
  final ApiClient _apiClient;

  ChatRepository(this._apiClient);

  /// Fetch chat history for an active ride.
  Future<Map<String, dynamic>> fetchHistory(String rideId) async {
    try {
      final response = await _apiClient.instance.get('/v1/logistics/rides/$rideId/chat/history');
      return response.data['data'];
    } on DioException catch (e) {
      throw e.response?.data['message'] ?? 'Failed to load chat history.';
    }
  }

  /// Send a chat message via REST (fallback or primary).
  Future<Map<String, dynamic>> sendMessage(String rideId, String content) async {
    try {
      final response = await _apiClient.instance.post('/v1/logistics/rides/$rideId/chat/send', data: {
        'content': content,
        'message_type': 'text',
      });
      return response.data['data'];
    } on DioException catch (e) {
      throw e.response?.data['message'] ?? 'Failed to send message.';
    }
  }
}
