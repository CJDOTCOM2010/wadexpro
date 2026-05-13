import 'package:flutter_riverpod/flutter_riverpod.dart';
import '../../../../core/network/api_provider.dart';
import '../../data/support_repository.dart';

final supportTicketsProvider = StateNotifierProvider<SupportTicketsNotifier, AsyncValue<List<dynamic>>>((ref) {
  final repository = ref.watch(supportTicketRepositoryProvider);
  return SupportTicketsNotifier(repository);
});

class SupportTicketsNotifier extends StateNotifier<AsyncValue<List<dynamic>>> {
  final SupportTicketRepository _repository;

  SupportTicketsNotifier(this._repository) : super(const AsyncValue.loading()) {
    fetchTickets();
  }

  Future<void> fetchTickets() async {
    state = const AsyncValue.loading();
    try {
      final tickets = await _repository.fetchTickets();
      state = AsyncValue.data(tickets);
    } catch (e, st) {
      state = AsyncValue.error(e, st);
    }
  }

  Future<void> createTicket(String subject, String category, String priority, String message) async {
    try {
      await _repository.createTicket(
        subject: subject,
        category: category,
        priority: priority,
        message: message,
      );
      await fetchTickets(); // Refresh list after creation
    } catch (e) {
      rethrow;
    }
  }

  Future<void> replyToTicket(String ticketId, String message) async {
    try {
      await _repository.replyToTicket(ticketId, message);
      await fetchTickets(); // Refresh list after reply
    } catch (e) {
      rethrow;
    }
  }
}
