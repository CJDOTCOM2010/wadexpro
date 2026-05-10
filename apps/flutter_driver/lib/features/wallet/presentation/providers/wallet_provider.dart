import 'package:flutter_riverpod/flutter_riverpod.dart';
import '../../data/wallet_repository.dart';
import '../../../../core/network/providers.dart';

class WalletState {
  final double balance;
  final String currency;
  final List<dynamic> transactions;
  final Map<String, dynamic>? weeklyStats;
  final bool isLoading;
  final String? error;

  WalletState({
    this.balance = 0.0,
    this.currency = 'GHS',
    this.transactions = const [],
    this.weeklyStats,
    this.isLoading = false,
    this.error,
  });

  WalletState copyWith({
    double? balance,
    String? currency,
    List<dynamic>? transactions,
    Map<String, dynamic>? weeklyStats,
    bool? isLoading,
    String? error,
  }) {
    return WalletState(
      balance: balance ?? this.balance,
      currency: currency ?? this.currency,
      transactions: transactions ?? this.transactions,
      weeklyStats: weeklyStats ?? this.weeklyStats,
      isLoading: isLoading ?? this.isLoading,
      error: error ?? this.error,
    );
  }
}

final walletRepositoryProvider = Provider((ref) {
  final apiClient = ref.watch(apiClientProvider);
  return WalletRepository(apiClient);
});

final walletProvider = StateNotifierProvider<WalletNotifier, WalletState>((ref) {
  final repository = ref.watch(walletRepositoryProvider);
  return WalletNotifier(repository);
});

class WalletNotifier extends StateNotifier<WalletState> {
  final WalletRepository _repository;

  WalletNotifier(this._repository) : super(WalletState()) {
    fetchWallet();
    fetchStats();
  }

  Future<void> fetchWallet() async {
    state = state.copyWith(isLoading: true, error: null);
    try {
      final balanceData = await _repository.getBalance();
      final historyData = await _repository.getTransactions();
      
      state = state.copyWith(
        balance: double.tryParse(balanceData['balance'].toString()) ?? 0.0,
        currency: balanceData['currency'] ?? 'GHS',
        transactions: historyData,
        isLoading: false,
      );
    } catch (e) {
      state = state.copyWith(isLoading: false, error: e.toString());
    }
  }

  Future<void> fetchStats() async {
    try {
      final summary = await _repository.getWeeklySummary();
      state = state.copyWith(weeklyStats: summary);
    } catch (e) {
      print('Stats fetch failed: $e');
    }
  }

  Future<void> refresh() async {
    await fetchWallet();
    await fetchStats();
  }
}
