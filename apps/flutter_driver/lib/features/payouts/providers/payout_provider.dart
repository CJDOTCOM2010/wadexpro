import '../../../core/network/providers.dart';
import '../../wallet/data/wallet_repository.dart';

class PayoutNotifier extends StateNotifier<AsyncValue<EarningsSummary>> {
  final WalletRepository _repository;

  PayoutNotifier(this._repository) : super(const AsyncValue.loading()) {
    getEarningsSummary();
  }

  Future<void> getEarningsSummary() async {
    state = const AsyncValue.loading();
    try {
      final balanceData = await _repository.getBalance();
      final txData = await _repository.getTransactions();
      
      final txs = txData.map((e) => Payout.fromJson(e)).toList();

      // For this demo, let's assume the balance API gives us some summary info, 
      // or we calculate it from the transactions if needed.
      // In a real app, the backend should provide this summary object directly.
      
      final today = DateTime.now();
      final dailyTotal = txs
          .where((t) => t.date.day == today.day && (t.status == 'completed' || t.status == 'paid'))
          .fold(0.0, (sum, t) => sum + t.amount);

      state = AsyncValue.data(EarningsSummary(
        dailyTotal: dailyTotal,
        weeklyTotal: balanceData['balance'], // Let's use current balance as a proxy for weekly for now
        monthlyTotal: balanceData['balance'] * 4, // Placeholder
        recentPayouts: txs.where((t) => t.status == 'completed' || t.status == 'paid').toList(),
      ));
    } catch (e, st) {
      state = AsyncValue.error(e, st);
    }
  }
}

final earningsSummaryProvider = StateNotifierProvider<PayoutNotifier, AsyncValue<EarningsSummary>>((ref) {
  final repository = ref.watch(walletRepositoryProvider);
  return PayoutNotifier(repository);
});
