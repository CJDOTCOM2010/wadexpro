import 'package:dio/dio.dart';
import 'package:flutter_riverpod/flutter_riverpod.dart';
import 'api_client.dart';
import '../services/socket_service.dart';
import '../../features/auth/data/auth_repository.dart';
import '../../features/auth/data/kyc_repository.dart';
import '../../features/wallet/data/wallet_repository.dart';
import '../../features/home/data/ride_repository.dart';

import '../../features/chat/data/chat_repository.dart';

final dioProvider = Provider((ref) => Dio());

// ... (existing providers)

final chatRepositoryProvider = Provider((ref) {
  final apiClient = ref.watch(apiClientProvider);
  return ChatRepository(apiClient);
});

final apiClientProvider = Provider((ref) {
  return ApiClient();
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

final kycRepositoryProvider = Provider((ref) {
  final apiClient = ref.watch(apiClientProvider);
  return KYCRepository(apiClient);
});

final rideRepositoryProvider = Provider((ref) {
  final apiClient = ref.watch(apiClientProvider);
  return RideRepository(apiClient);
});
