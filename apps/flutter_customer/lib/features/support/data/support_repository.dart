import 'package:dio/dio.dart';
import '../../../core/network/api_client.dart';

class SupportTicketRepository {
  final ApiClient _apiClient;

  SupportTicketRepository(this._apiClient);

  Future<List<dynamic>> fetchTickets() async {
    try {
      final response = await _apiClient.instance.get('/v1/logistics/support/tickets');
      return response.data['data'] ?? [];
    } on DioException catch (e) {
      throw e.response?.data['message'] ?? 'Failed to load tickets.';
    }
  }

  Future<Map<String, dynamic>> createTicket({
    required String subject,
    required String category,
    required String priority,
    required String message,
  }) async {
    try {
      final response = await _apiClient.instance.post('/v1/logistics/support/tickets', data: {
        'subject': subject,
        'category': category,
        'priority': priority,
        'message': message,
      });
      return response.data['data'];
    } on DioException catch (e) {
      throw e.response?.data['message'] ?? 'Failed to create ticket.';
    }
  }

  Future<Map<String, dynamic>> replyToTicket(String ticketId, String message) async {
    try {
      final response = await _apiClient.instance.post('/v1/logistics/support/tickets/$ticketId/reply', data: {
        'message': message,
      });
      return response.data['data'];
    } on DioException catch (e) {
      throw e.response?.data['message'] ?? 'Failed to send reply.';
    }
  }
}
