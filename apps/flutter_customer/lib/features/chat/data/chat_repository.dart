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

  /// Send a chat message via REST.
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

  /// Fetch active support chat
  Future<Map<String, dynamic>> fetchSupportHistory() async {
    try {
      final response = await _apiClient.instance.get('/v1/logistics/support/chat');
      return response.data['data'];
    } on DioException catch (e) {
      throw e.response?.data['message'] ?? 'Failed to load support chat.';
    }
  }

  /// Send support message
  Future<Map<String, dynamic>> sendSupportMessage(String content) async {
    try {
      final response = await _apiClient.instance.post('/v1/logistics/support/chat/send', data: {
        'content': content,
      });
      return response.data['data'];
    } on DioException catch (e) {
      throw e.response?.data['message'] ?? 'Failed to send support message.';
    }
  }
}
