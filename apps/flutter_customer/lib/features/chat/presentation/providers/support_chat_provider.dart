import 'package:flutter_riverpod/flutter_riverpod.dart';
import '../../../../core/network/api_provider.dart';
import '../../data/chat_repository.dart';
import 'chat_provider.dart';

final supportChatProvider = StateNotifierProvider<SupportChatNotifier, ChatState>((ref) {
  final socketService = ref.watch(socketServiceProvider);
  final repository = ref.watch(chatRepositoryProvider);
  return SupportChatNotifier(socketService, repository);
});

class SupportChatNotifier extends StateNotifier<ChatState> {
  final _socketService;
  final ChatRepository _repository;
  String? _conversationId;

  SupportChatNotifier(this._socketService, this._repository) : super(ChatState()) {
    _initializeListener();
    loadHistory();
  }

  Future<void> loadHistory() async {
    state = state.copyWith(isLoading: true);
    try {
      final data = await _repository.fetchSupportHistory();
      _conversationId = data['id'].toString();
      
      // Subscribe to the socket room for this conversation
      _socketService.socket?.emit('chat:subscribe', _conversationId);

      final List<dynamic> messagesJson = data['messages'] ?? [];
      
      final parsedMessages = messagesJson.map((m) {
        final isSystem = m['message_type'] == 'system';
        final isAdmin = m['sender']?['user_type'] == 'admin' || m['sender']?['user_type'] == 'super_admin';
        return ChatMessage(
          id: m['uuid'] ?? m['id'].toString(),
          text: m['content'] ?? '',
          timestamp: m['created_at'] != null ? DateTime.parse(m['created_at']) : DateTime.now(),
          isMe: !isSystem && !isAdmin,
          senderName: isSystem ? 'System' : (m['sender']?['name'] ?? 'Support Agent'),
        );
      }).toList();

      state = state.copyWith(messages: parsedMessages, isLoading: false);
    } catch (e) {
      state = state.copyWith(isLoading: false);
    }
  }

  void _initializeListener() {
    _socketService.onSupportMessage((data) {
       final msg = ChatMessage(
          id: DateTime.now().millisecondsSinceEpoch.toString(),
          text: data['message'] ?? '',
          timestamp: DateTime.now(),
          isMe: false,
          senderName: data['fromName'] ?? 'Support Agent',
        );
        state = state.copyWith(messages: [...state.messages, msg]);
    });
  }

  void sendMessage(String text) async {
    if (text.trim().isEmpty) return;

    // Optimistic Update
    final optimisticMsg = ChatMessage(
      id: DateTime.now().millisecondsSinceEpoch.toString(),
      text: text,
      timestamp: DateTime.now(),
      isMe: true,
      senderName: 'Me',
    );
    state = state.copyWith(messages: [...state.messages, optimisticMsg]);

    // Send via socket
    if (_conversationId != null) {
      _socketService.sendSupportMessage(_conversationId!, text);
    }

    // Persist via REST
    try {
      await _repository.sendSupportMessage(text);
    } catch (e) {
      // Handle error
    }
  }
  
  @override
  void dispose() {
    if (_conversationId != null) {
      _socketService.socket?.emit('chat:unsubscribe', _conversationId);
    }
    super.dispose();
  }
}
