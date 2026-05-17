import 'package:dio/dio.dart';
import 'package:flutter_riverpod/flutter_riverpod.dart';
import 'api_client.dart';
import '../services/socket_service.dart';
import '../../features/auth/data/auth_repository.dart';
import '../../features/wallet/data/wallet_repository.dart';
import '../../features/chat/data/chat_repository.dart';
import '../../features/support/data/support_repository.dart';
import '../providers/localization_provider.dart';

final dioProvider = Provider((ref) => Dio());

final apiClientProvider = Provider((ref) {
  final dio = ref.watch(dioProvider);
  final client = ApiClient(dio);
  
  // Sync locale from localization provider
  final locale = ref.watch(localizationProvider).locale;
  client.setLocale(locale);
  
  return client;
});

final chatRepositoryProvider = Provider((ref) {
  final apiClient = ref.watch(apiClientProvider);
  return ChatRepository(apiClient);
});

final authRepositoryProvider = Provider((ref) {
  final apiClient = ref.watch(apiClientProvider);
  return AuthRepository(apiClient);
});

final socketServiceProvider = Provider((ref) {
  return SocketService();
});

final walletRepositoryProvider = Provider((ref) {
  final apiClient = ref.watch(apiClientProvider);
  return WalletRepository(apiClient);
});

final supportTicketRepositoryProvider = Provider((ref) {
  final apiClient = ref.watch(apiClientProvider);
  return SupportTicketRepository(apiClient);
});
