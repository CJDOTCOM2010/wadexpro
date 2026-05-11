import 'package:flutter/material.dart';
import 'package:flutter_riverpod/flutter_riverpod.dart';
import '../../../../core/theme/app_colors.dart';
import '../../../../core/widgets/dynamic_glass_card.dart';
import '../providers/wallet_provider.dart';
import '../widgets/weekly_earnings_chart.dart';
import 'package:intl/intl.dart';

class WalletScreen extends ConsumerWidget {
  const WalletScreen({super.key});

  @override
  Widget build(BuildContext context, WidgetRef ref) {
    final walletState = ref.watch(walletProvider);
    final currencyFormat = NumberFormat.currency(symbol: walletState.currency == 'GHS' ? 'GH₵' : '${walletState.currency} ');

    return Scaffold(
      backgroundColor: AppColors.obsidianDark,
      appBar: AppBar(
        title: const Text('Earnings & Wallet', style: TextStyle(fontWeight: FontWeight.bold, color: Colors.white)),
        backgroundColor: Colors.transparent,
        elevation: 0,
        iconTheme: const IconThemeData(color: Colors.white),
        actions: [
          IconButton(
            onPressed: () => ref.read(walletProvider.notifier).refresh(),
            icon: const Icon(Icons.refresh, color: Colors.white),
          ),
        ],
      ),
      body: RefreshIndicator(
        onRefresh: () => ref.read(walletProvider.notifier).refresh(),
        color: AppColors.primary,
        backgroundColor: AppColors.obsidianDark,
        child: SingleChildScrollView(
          physics: const AlwaysScrollableScrollPhysics(),
          padding: const EdgeInsets.all(20),
          child: Column(
            crossAxisAlignment: CrossAxisAlignment.start,
            children: [
              _buildBalanceCard(walletState, currencyFormat),
              const SizedBox(height: 24),
              _buildInsightsSection(walletState, currencyFormat),
              const SizedBox(height: 32),
              const Text(
                'Recent Transactions',
                style: TextStyle(fontSize: 20, fontWeight: FontWeight.bold, color: Colors.white),
              ),
              const SizedBox(height: 16),
              if (walletState.isLoading && walletState.transactions.isEmpty)
                const Center(child: CircularProgressIndicator(color: AppColors.primary))
              else if (walletState.transactions.isEmpty)
                const Center(child: Padding(
                  padding: EdgeInsets.all(40.0),
                  child: Text('No transactions yet.', style: TextStyle(color: Colors.white54)),
                ))
              else
                ...walletState.transactions.map((tx) => _buildTransactionTile(tx, currencyFormat)),
              const SizedBox(height: 32),
              _buildPayoutMethodsSection(context, walletState),
              const SizedBox(height: 32),
              _buildReferralSection(context, walletState),
              const SizedBox(height: 40),
            ],
          ),
        ),
      ),
    );
  }

  Widget _buildPayoutMethodsSection(BuildContext context, WalletState state) {
    return Column(
      crossAxisAlignment: CrossAxisAlignment.start,
      children: [
        const Row(
          mainAxisAlignment: MainAxisAlignment.spaceBetween,
          children: [
            Text('Payout Methods', style: TextStyle(fontSize: 20, fontWeight: FontWeight.bold, color: Colors.white)),
            Icon(Icons.add_circle_outline, color: Colors.white70, size: 24),
          ],
        ),
        const SizedBox(height: 16),
        DynamicGlassCard(
          padding: const EdgeInsets.all(20),
          child: Row(
            children: [
              Container(
                padding: const EdgeInsets.all(12),
                decoration: BoxDecoration(color: Colors.white.withOpacity(0.1), shape: BoxShape.circle),
                child: const Icon(Icons.account_balance, color: Colors.white),
              ),
              const SizedBox(width: 16),
              const Expanded(
                child: Column(
                  crossAxisAlignment: CrossAxisAlignment.start,
                  children: [
                    Text('Bank Account', style: TextStyle(fontWeight: FontWeight.bold, color: Colors.white, fontSize: 16)),
                    Text('Default payout method', style: TextStyle(fontSize: 13, color: Colors.white54)),
                  ],
                ),
              ),
              const Icon(Icons.check_circle, color: Colors.greenAccent, size: 24),
            ],
          ),
        ),
      ],
    );
  }

  Widget _buildReferralSection(BuildContext context, WalletState state) {
    return Container(
      padding: const EdgeInsets.all(24),
      decoration: BoxDecoration(
        gradient: AppColors.luxuryGradient,
        borderRadius: BorderRadius.circular(24),
        boxShadow: [
          BoxShadow(
            color: AppColors.primary.withOpacity(0.3),
            blurRadius: 20,
            offset: const Offset(0, 10),
          )
        ],
      ),
      child: Column(
        children: [
          const Row(
            children: [
              Icon(Icons.people_alt, color: AppColors.accent, size: 28),
              SizedBox(width: 12),
              Text('Refer a Colleague', style: TextStyle(color: Colors.white, fontWeight: FontWeight.bold, fontSize: 18)),
            ],
          ),
          const SizedBox(height: 16),
          const Text(
            'Invite other drivers to WADEXPRO. Get a bonus for every driver who completes their first 10 trips.',
            style: TextStyle(color: Colors.white70, fontSize: 14),
          ),
          const SizedBox(height: 20),
          Container(
            padding: const EdgeInsets.symmetric(horizontal: 16, vertical: 12),
            decoration: BoxDecoration(color: Colors.black26, borderRadius: BorderRadius.circular(12)),
            child: const Row(
              mainAxisAlignment: MainAxisAlignment.spaceBetween,
              children: [
                Text('WADEX-DRV-77', style: TextStyle(color: Colors.white, fontWeight: FontWeight.bold, letterSpacing: 2)),
                Icon(Icons.copy, color: Colors.white54, size: 18),
              ],
            ),
          ),
        ],
      ),
    );
  }

  Widget _buildBalanceCard(WalletState state, NumberFormat format) {
    return Container(
      width: double.infinity,
      padding: const EdgeInsets.all(30),
      decoration: BoxDecoration(
        color: Colors.white.withOpacity(0.05),
        border: Border.all(color: Colors.white.withOpacity(0.1)),
        borderRadius: BorderRadius.circular(24),
      ),
      child: Column(
        children: [
          const Text('CURRENT BALANCE', style: TextStyle(color: Colors.white54, fontSize: 12, letterSpacing: 1.2)),
          const SizedBox(height: 8),
          Text(
            format.format(state.balance),
            style: const TextStyle(color: Colors.white, fontSize: 40, fontWeight: FontWeight.bold),
          ),
          const SizedBox(height: 24),
          Row(
            children: [
              Expanded(
                child: _buildActionButton(Icons.arrow_upward, 'Withdraw', color: AppColors.accent),
              ),
              const SizedBox(width: 16),
              Expanded(
                child: _buildActionButton(Icons.add, 'Add Funds', color: Colors.white),
              ),
            ],
          ),
        ],
      ),
    );
  }

  Widget _buildInsightsSection(WalletState state, NumberFormat format) {
    final totalWeekly = (state.weeklyStats?['total_weekly'] as num?)?.toDouble() ?? 0.0;

    return DynamicGlassCard(
      padding: const EdgeInsets.all(20),
      child: Column(
        crossAxisAlignment: CrossAxisAlignment.start,
        children: [
          Row(
            mainAxisAlignment: MainAxisAlignment.spaceBetween,
            children: [
              const Text('WEEKLY INSIGHTS', style: TextStyle(color: Colors.white70, fontSize: 10, fontWeight: FontWeight.bold, letterSpacing: 1)),
              Text(
                'Total: ${format.format(totalWeekly)}',
                style: const TextStyle(color: Colors.white, fontWeight: FontWeight.bold, fontSize: 14),
              ),
            ],
          ),
          const SizedBox(height: 16),
          WeeklyEarningsChart(stats: state.weeklyStats),
          const SizedBox(height: 16),
          const Divider(color: Colors.white10),
          Padding(
            padding: const EdgeInsets.symmetric(vertical: 10),
            child: Row(
              mainAxisAlignment: MainAxisAlignment.spaceAround,
              children: [
                _buildSimpleStat('Growth', '+12%', Colors.greenAccent),
                _buildSimpleStat('Trips', '${_calculateTotalTrips(state)}', Colors.white),
                _buildSimpleStat('Avg/Day', format.format(totalWeekly / 7), Colors.white),
              ],
            ),
          ),
        ],
      ),
    );
  }

  int _calculateTotalTrips(WalletState state) {
    if (state.weeklyStats == null) return 0;
    final days = state.weeklyStats!['days'] as List;
    int total = 0;
    for (var day in days) {
      total += (day['trip_count'] as num).toInt();
    }
    return total;
  }

  Widget _buildSimpleStat(String label, String value, Color valueColor) {
    return Column(
      children: [
        Text(label, style: const TextStyle(color: Colors.white54, fontSize: 12)),
        const SizedBox(height: 4),
        Text(value, style: TextStyle(color: valueColor, fontWeight: FontWeight.bold, fontSize: 16)),
      ],
    );
  }

  Widget _buildActionButton(IconData icon, String label, {required Color color}) {
    return Container(
      padding: const EdgeInsets.symmetric(vertical: 14),
      decoration: BoxDecoration(
        color: color.withOpacity(0.1),
        borderRadius: BorderRadius.circular(16),
        border: Border.all(color: color.withOpacity(0.2)),
      ),
      child: Row(
        mainAxisAlignment: MainAxisAlignment.center,
        children: [
          Icon(icon, color: color, size: 20),
          const SizedBox(width: 8),
          Text(label, style: TextStyle(color: color, fontWeight: FontWeight.bold, fontSize: 15)),
        ],
      ),
    );
  }

  Widget _buildTransactionTile(dynamic tx, NumberFormat format) {
    final bool isCredit = tx['type'] == 'credit';
    final amount = (tx['amount'] as num).toDouble();
    final date = DateTime.parse(tx['created_at']);

    return Padding(
      padding: const EdgeInsets.only(bottom: 12),
      child: DynamicGlassCard(
        padding: const EdgeInsets.all(16),
        child: Row(
          children: [
            Container(
              padding: const EdgeInsets.all(12),
              decoration: BoxDecoration(
                color: (isCredit ? Colors.greenAccent : Colors.redAccent).withOpacity(0.1),
                shape: BoxShape.circle,
              ),
              child: Icon(
                isCredit ? Icons.arrow_downward : Icons.arrow_upward,
                color: isCredit ? Colors.greenAccent : Colors.redAccent,
                size: 20,
              ),
            ),
            const SizedBox(width: 16),
            Expanded(
              child: Column(
                crossAxisAlignment: CrossAxisAlignment.start,
                children: [
                  Text(tx['description'] ?? 'Transaction', style: const TextStyle(fontWeight: FontWeight.bold, color: Colors.white, fontSize: 15)),
                  const SizedBox(height: 4),
                  Text(DateFormat('MMM dd, yyyy • hh:mm a').format(date), style: const TextStyle(color: Colors.white54, fontSize: 12)),
                ],
              ),
            ),
            Text(
              '${isCredit ? '+' : '-'}${format.format(amount)}',
              style: TextStyle(
                fontWeight: FontWeight.bold,
                fontSize: 16,
                color: isCredit ? Colors.greenAccent : Colors.redAccent,
              ),
            ),
          ],
        ),
      ),
    );
  }
}
