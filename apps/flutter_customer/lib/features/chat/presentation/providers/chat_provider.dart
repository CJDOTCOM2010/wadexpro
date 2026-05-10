import 'package:flutter_riverpod/flutter_riverpod.dart';
import '../../../../core/network/api_provider.dart';
import '../../data/chat_repository.dart';

class ChatMessage {
  final String id;
  final String text;
  final DateTime timestamp;
  final bool isMe;
  final String senderName;

  ChatMessage({
    required this.id,
    required this.text,
    required this.timestamp,
    required this.isMe,
    required this.senderName,
  });

  factory ChatMessage.fromMap(Map<String, dynamic> map, int currentUserId) {
    return ChatMessage(
      id: map['uuid'] ?? map['id'].toString(),
      text: map['content'] ?? map['message'] ?? '',
      timestamp: map['created_at'] != null ? DateTime.parse(map['created_at']) : DateTime.now(),
      isMe: map['sender_id'] == currentUserId,
      senderName: map['sender']?['name'] ?? 'User',
    );
  }
}

class ChatState {
  final List<ChatMessage> messages;
  final bool isLoading;

  ChatState({this.messages = const [], this.isLoading = false});

  ChatState copyWith({List<ChatMessage>? messages, bool? isLoading}) {
    return ChatState(
      messages: messages ?? this.messages,
      isLoading: isLoading ?? this.isLoading,
    );
  }
}

final chatProvider = StateNotifierProvider.family<ChatNotifier, ChatState, String>((ref, rideId) {
  final socketService = ref.watch(socketServiceProvider);
  final repository = ref.watch(chatRepositoryProvider);
  return ChatNotifier(socketService, repository, rideId);
});

class ChatNotifier extends StateNotifier<ChatState> {
  final _socketService;
  final ChatRepository _repository;
  final String _rideId;

  ChatNotifier(this._socketService, this._repository, this._rideId) : super(ChatState()) {
    _initializeListener();
    loadHistory();
  }

  Future<void> loadHistory() async {
    state = state.copyWith(isLoading: true);
    try {
      final data = await _repository.fetchHistory(_rideId);
      final List<dynamic> messagesJson = data['messages'];
      // Note: We need current user ID for 'isMe'. In a real app, inject it from AuthStore.
      // Assuming a placeholder comparison (0) for now as implemented in the Driver sync.
      final messages = messagesJson.map((m) => ChatMessage.fromMap(m, 0)).toList(); 
      state = state.copyWith(messages: messages, isLoading: false);
    } catch (e) {
      state = state.copyWith(isLoading: false);
    }
  }

  void _initializeListener() {
    _socketService.onChatMessage((data) {
      if (data['rideId'] == _rideId) {
        final msg = ChatMessage.fromMap(data, -1); // Received messages are never 'me'
        state = state.copyWith(messages: [...state.messages, msg]);
      }
    });
  }

  void sendMessage(String text) async {
    if (text.trim().isEmpty) return;

    // 1. Optimistic Update
    final optimisticMsg = ChatMessage(
      id: DateTime.now().millisecondsSinceEpoch.toString(),
      text: text,
      timestamp: DateTime.now(),
      isMe: true,
      senderName: 'Me',
    );
    state = state.copyWith(messages: [...state.messages, optimisticMsg]);

    // 2. Real-time Relay
    _socketService.sendChatMessage(_rideId, text);

    // 3. Persistent Sync
    try {
      await _repository.sendMessage(_rideId, text);
    } catch (e) {
      // Handle fail (optional: retry or show error bubble)
    }
  }
}
