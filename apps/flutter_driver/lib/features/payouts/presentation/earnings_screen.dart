import 'package:flutter/material.dart';
import 'package:flutter_riverpod/flutter_riverpod.dart';
import '../providers/payout_provider.dart';
import '../models/payout.dart';

class MyEarningsScreen extends ConsumerWidget {
  const MyEarningsScreen({super.key});

  @override
  Widget build(BuildContext context, WidgetRef ref) {
    final earningsAsync = ref.watch(earningsSummaryProvider);

    return Scaffold(
      backgroundColor: const Color(0xFFF8FAFC),
      appBar: AppBar(
        title: const Text('My Earnings', style: TextStyle(fontWeight: FontWeight.bold, color: Colors.black)),
        backgroundColor: Colors.white,
        elevation: 0,
        centerTitle: true,
      ),
      body: earningsAsync.when(
        data: (summary) => RefreshIndicator(
          onRefresh: () => ref.read(earningsSummaryProvider.notifier).getEarningsSummary(),
          child: SingleChildScrollView(
            physics: const AlwaysScrollableScrollPhysics(),
            padding: const EdgeInsets.all(20),
            child: Column(
              crossAxisAlignment: CrossAxisAlignment.start,
              children: [
                _buildMainStats(summary),
                const SizedBox(height: 30),
                const Text('Performance Trends', style: TextStyle(fontSize: 18, fontWeight: FontWeight.bold)),
                const SizedBox(height: 15),
                _buildEarningsChart(),
                const SizedBox(height: 30),
                const Text('Recent Payouts', style: TextStyle(fontSize: 18, fontWeight: FontWeight.bold)),
                const SizedBox(height: 15),
                if (summary.recentPayouts.isEmpty)
                  const Center(child: Padding(padding: EdgeInsets.all(20), child: Text('No recent payouts', style: TextStyle(color: Colors.grey))))
                else
                  ...summary.recentPayouts.map((p) => _buildPayoutItem(p)),
              ],
            ),
          ),
        ),
        loading: () => const Center(child: CircularProgressIndicator()),
        error: (err, stack) => Center(
          child: Column(
            mainAxisAlignment: MainAxisAlignment.center,
            children: [
              Text('Error: $err'),
              TextButton(
                onPressed: () => ref.read(earningsSummaryProvider.notifier).getEarningsSummary(),
                child: const Text('Retry'),
              )
            ],
          ),
        ),
      ),
    );
  }

  Widget _buildMainStats(summary) {
    return Container(
      padding: const EdgeInsets.all(24),
      decoration: BoxDecoration(
        gradient: const LinearGradient(colors: [Color(0xFF2563EB), Color(0xFF1E40AF)]),
        borderRadius: BorderRadius.circular(24),
        boxShadow: [BoxShadow(color: Colors.blue.withOpacity(0.3), blurRadius: 20, offset: const Offset(0, 10))],
      ),
      child: Column(
        children: [
          const Text('Total Weekly Earnings', style: TextStyle(color: Colors.white70, fontSize: 14)),
          const SizedBox(height: 8),
          Text('GHS ${summary.weeklyTotal.toStringAsFixed(2)}', 
            style: const TextStyle(color: Colors.white, fontSize: 32, fontWeight: FontWeight.w900)),
          const SizedBox(height: 20),
          Row(
            mainAxisAlignment: MainAxisAlignment.spaceAround,
            children: [
              _statItem('Daily', 'GHS ${summary.dailyTotal.toStringAsFixed(2)}'),
              Container(width: 1, height: 30, color: Colors.white24),
              _statItem('Monthly', 'GHS ${summary.monthlyTotal.toStringAsFixed(0)}'),
            ],
          )
        ],
      ),
    );
  }

  Widget _statItem(String label, String value) {
    return Column(
      children: [
        Text(label, style: const TextStyle(color: Colors.white70, fontSize: 12)),
        const SizedBox(height: 4),
        Text(value, style: const TextStyle(color: Colors.white, fontSize: 16, fontWeight: FontWeight.bold)),
      ],
    );
  }

  Widget _buildEarningsChart() {
    return Container(
      height: 200,
      padding: const EdgeInsets.all(16),
      decoration: BoxDecoration(color: Colors.white, borderRadius: BorderRadius.circular(20)),
      child: LineChart(
        LineChartData(
          gridData: const FlGridData(show: false),
          titlesData: const FlTitlesData(show: false),
          borderData: FlBorderData(show: false),
          lineBarsData: [
            LineChartBarData(
              isCurved: true,
              color: Colors.blue,
              barWidth: 4,
              isStrokeCapRound: true,
              dotData: const FlDotData(show: false),
              belowBarData: BarAreaData(show: true, color: Colors.blue.withOpacity(0.1)),
              spots: const [
                FlSpot(0, 30), FlSpot(1, 45), FlSpot(2, 35),
                FlSpot(3, 80), FlSpot(4, 55), FlSpot(5, 70), FlSpot(6, 65),
              ],
            ),
          ],
        ),
      ),
    );
  }

  Widget _buildPayoutItem(payout) {
    return Container(
      margin: const EdgeInsets.only(bottom: 12),
      padding: const EdgeInsets.all(16),
      decoration: BoxDecoration(color: Colors.white, borderRadius: BorderRadius.circular(16)),
      child: Row(
        children: [
          CircleAvatar(backgroundColor: Colors.green.withOpacity(0.1), child: const Icon(Icons.wallet, color: Colors.green, size: 20)),
          const SizedBox(width: 16),
          Expanded(
            child: Column(crossAxisAlignment: CrossAxisAlignment.start, children: [
              Text(payout.reference, style: const TextStyle(fontWeight: FontWeight.bold)),
              Text(payout.date.toString().split(' ')[0], style: const TextStyle(color: Colors.grey, fontSize: 12)),
            ]),
          ),
          Text('GHS ${payout.amount.toStringAsFixed(2)}', style: const TextStyle(fontWeight: FontWeight.w900, fontSize: 16)),
        ],
      ),
    );
  }
}
