import 'package:flutter_riverpod/flutter_riverpod.dart';
import '../../data/wallet_repository.dart';
import '../../../../core/network/api_provider.dart';

class WalletState {
  final double balance;
  final String currency;
  final List<dynamic> transactions;
  final List<dynamic> paymentMethods;
  final int referralCount;
  final String? referralCode;
  final bool isLoading;
  final String? errorMessage;

  WalletState({
    this.balance = 0.0,
    this.currency = 'GHS',
    this.transactions = const [],
    this.paymentMethods = const [],
    this.referralCount = 0,
    this.referralCode,
    this.isLoading = false,
    this.errorMessage,
  });

  WalletState copyWith({
    double? balance,
    String? currency,
    List<dynamic>? transactions,
    List<dynamic>? paymentMethods,
    int? referralCount,
    String? referralCode,
    bool? isLoading,
    String? errorMessage,
  }) {
    return WalletState(
      balance: balance ?? this.balance,
      currency: currency ?? this.currency,
      transactions: transactions ?? this.transactions,
      paymentMethods: paymentMethods ?? this.paymentMethods,
      referralCount: referralCount ?? this.referralCount,
      referralCode: referralCode ?? this.referralCode,
      isLoading: isLoading ?? this.isLoading,
      errorMessage: errorMessage,
    );
  }
}

final walletProvider = StateNotifierProvider<WalletNotifier, WalletState>((ref) {
  final repository = ref.watch(walletRepositoryProvider);
  return WalletNotifier(repository);
});

class WalletNotifier extends StateNotifier<WalletState> {
  final WalletRepository _repository;

  WalletNotifier(this._repository) : super(WalletState()) {
    refresh();
  }

  Future<void> refresh() async {
    state = state.copyWith(isLoading: true);
    try {
      final hubData = await _repository.getHubData();
      final txData = await _repository.getTransactions();
      
      state = state.copyWith(
        balance: double.tryParse(hubData['balance'].toString()) ?? 0.0,
        currency: hubData['currency'] ?? 'GHS',
        paymentMethods: hubData['payment_methods'] ?? [],
        referralCount: hubData['referral_count'] ?? 0,
        referralCode: hubData['referral_code'],
        transactions: txData,
        isLoading: false,
      );
    } catch (e) {
      state = state.copyWith(isLoading: false, errorMessage: e.toString());
    }
  }

  Future<bool> redeemPromo(String code) async {
    try {
      await _repository.checkPromo(code);
      await refresh();
      return true;
    } catch (e) {
      return false;
    }
  }

  Future<String?> initializeTopUp(double amount) async {
    try {
      final result = await _repository.initializeTopUp(amount);
      // The production backend returns the gateway link in 'authorization_url'
      return result['authorization_url'];
    } catch (e) {
      return null;
    }
  }

  Future<void> verifyTopUp(String reference) async {
    state = state.copyWith(isLoading: true);
    try {
      await _repository.verifyTopUp(reference);
      await refresh();
    } catch (e) {
      state = state.copyWith(isLoading: false, errorMessage: 'Verification failed');
    }
  }
}
