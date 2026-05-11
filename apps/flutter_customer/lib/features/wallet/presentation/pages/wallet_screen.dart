import 'package:flutter/material.dart';
import 'package:flutter/services.dart';
import 'package:flutter_riverpod/flutter_riverpod.dart';
import 'package:intl/intl.dart';
import 'package:webview_flutter/webview_flutter.dart';
import '../../../../core/theme/app_colors.dart';
import '../providers/wallet_provider.dart';

class WalletScreen extends ConsumerWidget {
  const WalletScreen({super.key});

  @override
  Widget build(BuildContext context, WidgetRef ref) {
    final walletState = ref.watch(walletProvider);

    return Scaffold(
      backgroundColor: AppColors.obsidianDark,
      appBar: AppBar(
        title: const Text('WADEX Wallet', style: TextStyle(fontWeight: FontWeight.bold, color: Colors.white)),
        centerTitle: true,
        backgroundColor: Colors.transparent,
        elevation: 0,
        foregroundColor: Colors.white,
        leading: IconButton(icon: const Icon(Icons.arrow_back, color: Colors.white), onPressed: () => Navigator.pop(context)),
        actions: [
          IconButton(
            onPressed: () => ref.read(walletProvider.notifier).refresh(),
            icon: const Icon(Icons.refresh, color: Colors.white),
          ),
        ],
      ),
      body: RefreshIndicator(
        onRefresh: () => ref.read(walletProvider.notifier).refresh(),
        color: Colors.white,
        backgroundColor: AppColors.obsidianDark,
        child: SingleChildScrollView(
          physics: const AlwaysScrollableScrollPhysics(),
          child: Column(
            crossAxisAlignment: CrossAxisAlignment.start,
            children: [
              _buildBalanceCard(context, walletState),
              _buildTripProfilesSection(context, walletState),
              _buildPaymentMethodsSection(context, walletState),
              _buildPromotionsSection(context, ref),
              _buildReferralSection(context, walletState),
              Padding(
                padding: const EdgeInsets.fromLTRB(24, 32, 24, 16),
                child: Text('Quick Top-up', style: TextStyle(fontSize: 18, fontWeight: FontWeight.bold, color: Colors.white)),
              ),
              _buildQuickTopUp(context, ref),
              _buildInStoreAgentCta(context),
              Padding(
                padding: const EdgeInsets.fromLTRB(24, 32, 24, 16),
                child: Text('Recent Activity', style: TextStyle(fontSize: 18, fontWeight: FontWeight.bold, color: Colors.white)),
              ),
              if (walletState.isLoading && walletState.transactions.isEmpty)
                const Center(child: Padding(padding: EdgeInsets.all(40), child: CircularProgressIndicator(color: Colors.white)))
              else if (walletState.transactions.isEmpty)
                _buildEmptyState()
              else
                _buildTransactionList(walletState.transactions),
              const SizedBox(height: 40),
            ],
          ),
        ),
      ),
    );
  }

  Widget _buildTripProfilesSection(BuildContext context, WalletState state) {
    return Padding(
      padding: const EdgeInsets.symmetric(horizontal: 24),
      child: Column(
        crossAxisAlignment: CrossAxisAlignment.start,
        children: [
          const Text('Trip Profiles', style: TextStyle(fontWeight: FontWeight.bold, fontSize: 18, color: Colors.white)),
          const SizedBox(height: 12),
          Container(
            padding: const EdgeInsets.all(16),
            decoration: BoxDecoration(
              color: Colors.white.withOpacity(0.08),
              borderRadius: BorderRadius.circular(20),
              border: Border.all(color: Colors.white12),
            ),
            child: Row(
              children: [
                Container(
                  padding: const EdgeInsets.all(8),
                  decoration: BoxDecoration(color: const Color(0xFF6C63FF).withOpacity(0.2), borderRadius: BorderRadius.circular(12)),
                  child: const Icon(Icons.person, color: Color(0xFF6C63FF), size: 20),
                ),
                const SizedBox(width: 16),
                const Expanded(
                  child: Column(
                    crossAxisAlignment: CrossAxisAlignment.start,
                    children: [
                      Text('Personal Profile', style: TextStyle(fontWeight: FontWeight.bold, color: Colors.white)),
                      Text('Default individual billing', style: TextStyle(fontSize: 12, color: Colors.white54)),
                    ],
                  ),
                ),
                Container(
                  padding: const EdgeInsets.symmetric(horizontal: 8, vertical: 4),
                  decoration: BoxDecoration(color: const Color(0xFF00D4AA).withOpacity(0.15), borderRadius: BorderRadius.circular(8)),
                  child: const Text('ACTIVE', style: TextStyle(fontSize: 10, fontWeight: FontWeight.bold, color: Color(0xFF00D4AA))),
                ),
              ],
            ),
          ),
        ],
      ),
    );
  }

  Widget _buildPaymentMethodsSection(BuildContext context, WalletState state) {
    return Padding(
      padding: const EdgeInsets.fromLTRB(24, 32, 24, 0),
      child: Column(
        crossAxisAlignment: CrossAxisAlignment.start,
        children: [
          Row(
            mainAxisAlignment: MainAxisAlignment.spaceBetween,
            children: [
              const Text('Payment Methods', style: TextStyle(fontWeight: FontWeight.bold, fontSize: 18, color: Colors.white)),
              Icon(Icons.add_circle_outline, color: Colors.white54, size: 20),
            ],
          ),
          const SizedBox(height: 12),
          if (state.paymentMethods.isEmpty)
            _buildEmptyPaymentState()
          else
            ...state.paymentMethods.map((pm) => _buildPaymentMethodTile(pm)),
        ],
      ),
    );
  }

  Widget _buildEmptyPaymentState() {
    return Container(
      width: double.infinity,
      padding: const EdgeInsets.all(24),
      decoration: BoxDecoration(
        color: Colors.white.withOpacity(0.05),
        borderRadius: BorderRadius.circular(20),
        border: Border.all(color: Colors.white12),
      ),
      child: const Column(
        children: [
          Icon(Icons.credit_card, color: Colors.white24),
          SizedBox(height: 8),
          Text('No cards or accounts linked', style: TextStyle(color: Colors.white38, fontSize: 12)),
        ],
      ),
    );
  }

  Widget _buildPaymentMethodTile(dynamic pm) {
    return ListTile(
      contentPadding: EdgeInsets.zero,
      leading: Container(
        padding: const EdgeInsets.all(8),
        decoration: BoxDecoration(color: const Color(0xFFF9CA24).withOpacity(0.15), borderRadius: BorderRadius.circular(12)),
        child: Icon(pm['provider'] == 'CARD' ? Icons.credit_card : Icons.wallet_membership, color: const Color(0xFFF9CA24), size: 20),
      ),
      title: Text('${pm['brand'] ?? pm['provider']} •••• ${pm['last_four'] ?? ''}', style: const TextStyle(color: Colors.white)),
      trailing: pm['is_default'] ? const Icon(Icons.check_circle, color: Color(0xFF00D4AA)) : null,
    );
  }

  Widget _buildPromotionsSection(BuildContext context, WidgetRef ref) {
    final controller = TextEditingController();
    return Padding(
      padding: const EdgeInsets.fromLTRB(24, 32, 24, 0),
      child: Column(
        crossAxisAlignment: CrossAxisAlignment.start,
        children: [
          const Text('Promotions & Vouchers', style: TextStyle(fontWeight: FontWeight.bold, fontSize: 18, color: Colors.white)),
          const SizedBox(height: 12),
          TextField(
            controller: controller,
            style: const TextStyle(color: Colors.white),
            decoration: InputDecoration(
              hintText: 'Enter Promo Code',
              hintStyle: const TextStyle(color: Colors.white38),
              filled: true,
              fillColor: Colors.white.withOpacity(0.08),
              border: OutlineInputBorder(borderRadius: BorderRadius.circular(16), borderSide: BorderSide.none),
              enabledBorder: OutlineInputBorder(borderRadius: BorderRadius.circular(16), borderSide: BorderSide(color: Colors.white12)),
              focusedBorder: OutlineInputBorder(borderRadius: BorderRadius.circular(16), borderSide: BorderSide(color: Colors.white24)),
              suffixIcon: IconButton(
                onPressed: () async {
                   final success = await ref.read(walletProvider.notifier).redeemPromo(controller.text);
                   if (context.mounted) {
                     ScaffoldMessenger.of(context).showSnackBar(SnackBar(
                       content: Text(success ? 'Promo Applied!' : 'Invalid Promo Code'),
                       backgroundColor: success ? const Color(0xFF00D4AA) : const Color(0xFFFF6B6B),
                       behavior: SnackBarBehavior.floating,
                       shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(12)),
                     ));
                   }
                },
                icon: const Icon(Icons.arrow_forward, color: Colors.white54),
              ),
            ),
          ),
        ],
      ),
    );
  }

  Widget _buildReferralSection(BuildContext context, WalletState state) {
    return Container(
      margin: const EdgeInsets.fromLTRB(24, 32, 24, 0),
      padding: const EdgeInsets.all(24),
      decoration: BoxDecoration(
        gradient: const LinearGradient(colors: [Color(0xFF00D4AA), Color(0xFF00A87D)]),
        borderRadius: BorderRadius.circular(24),
        boxShadow: [BoxShadow(color: const Color(0xFF00D4AA).withOpacity(0.3), blurRadius: 20, offset: const Offset(0, 10))],
      ),
      child: Column(
        children: [
          const Row(
            children: [
              Icon(Icons.stars, color: Colors.white, size: 28),
              SizedBox(width: 12),
              Text('Refer & Earn GHS 20', style: TextStyle(color: Colors.white, fontWeight: FontWeight.bold, fontSize: 16)),
            ],
          ),
          const SizedBox(height: 16),
          const Text(
            'Share your referral code with friends. When they complete their first trip, you both get GHS 20 credit.',
            style: TextStyle(color: Colors.white70, fontSize: 12),
          ),
          const SizedBox(height: 24),
          Container(
            padding: const EdgeInsets.symmetric(horizontal: 16, vertical: 12),
            decoration: BoxDecoration(color: Colors.white.withOpacity(0.15), borderRadius: BorderRadius.circular(12)),
            child: GestureDetector(
              onTap: () {
                Clipboard.setData(ClipboardData(text: state.referralCode ?? 'WADEXPRO'));
                ScaffoldMessenger.of(context).showSnackBar(
                  SnackBar(
                    content: const Text('Referral code copied to clipboard!'),
                    backgroundColor: const Color(0xFF00D4AA),
                    behavior: SnackBarBehavior.floating,
                    shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(12)),
                  ),
                );
              },
              child: Row(
                mainAxisAlignment: MainAxisAlignment.spaceBetween,
                children: [
                  Text(state.referralCode ?? 'WADEXPRO', style: const TextStyle(color: Colors.white, fontWeight: FontWeight.bold, letterSpacing: 2)),
                  const Row(
                    children: [
                      Icon(Icons.copy, color: Colors.white, size: 18),
                      SizedBox(width: 6),
                      Text('Copy', style: TextStyle(color: Colors.white70, fontSize: 12)),
                    ],
                  ),
                ],
              ),
            ),
          ),
        ],
      ),
    );
  }

  Widget _buildInStoreAgentCta(BuildContext context) {
    return Container(
      margin: const EdgeInsets.fromLTRB(24, 24, 24, 0),
      padding: const EdgeInsets.all(20),
      decoration: BoxDecoration(
        color: Colors.white.withOpacity(0.08),
        borderRadius: BorderRadius.circular(20),
        border: Border.all(color: Colors.white12),
      ),
      child: Row(
        children: [
          Container(
            padding: const EdgeInsets.all(12),
            decoration: BoxDecoration(color: const Color(0xFFE17055).withOpacity(0.15), shape: BoxShape.circle),
            child: const Icon(Icons.storefront, color: Color(0xFFE17055)),
          ),
          const SizedBox(width: 16),
          const Expanded(
            child: Column(
              crossAxisAlignment: CrossAxisAlignment.start,
              children: [
                Text('In-Store Top-up', style: TextStyle(fontWeight: FontWeight.bold, color: Colors.white)),
                Text('Visit any WADEX Office to top up via Cash.', style: TextStyle(fontSize: 11, color: Colors.white54)),
              ],
            ),
          ),
          const Icon(Icons.chevron_right, color: Colors.white38),
        ],
      ),
    );
  }

  Widget _buildBalanceCard(BuildContext context, WalletState state) {
    return Container(
      width: double.infinity,
      margin: const EdgeInsets.all(24),
      padding: const EdgeInsets.all(28),
      decoration: BoxDecoration(
        gradient: const LinearGradient(
          begin: Alignment.topLeft,
          end: Alignment.bottomRight,
          colors: [Color(0xFF6C63FF), Color(0xFF4834DF)],
        ),
        borderRadius: BorderRadius.circular(28),
        boxShadow: [BoxShadow(color: const Color(0xFF6C63FF).withOpacity(0.35), blurRadius: 25, offset: const Offset(0, 12))],
      ),
      child: Column(
        crossAxisAlignment: CrossAxisAlignment.start,
        children: [
          Row(
            mainAxisAlignment: MainAxisAlignment.spaceBetween,
            children: [
              const Text('Available Balance', style: TextStyle(color: Colors.white70, fontSize: 16)),
              const Icon(Icons.wallet, color: Colors.white24, size: 24),
            ],
          ),
          const SizedBox(height: 12),
          Text(
            '${state.currency} ${state.balance.toStringAsFixed(2)}',
            style: const TextStyle(color: Colors.white, fontSize: 36, fontWeight: FontWeight.w900, letterSpacing: 1.2),
          ),
          const SizedBox(height: 32),
          Row(
            children: [
              _buildCardAction(Icons.add, 'Top Up', () => _showTopUpDialog(context)),
              const SizedBox(width: 24),
              _buildCardAction(Icons.history, 'Statement', () => _showStatement(context, state)),
            ],
          )
        ],
      ),
    );
  }

  Widget _buildCardAction(IconData icon, String label, VoidCallback onTap) {
    return InkWell(
      onTap: onTap,
      child: Row(
        children: [
          Container(
            padding: const EdgeInsets.all(8),
            decoration: BoxDecoration(color: Colors.white.withOpacity(0.2), shape: BoxShape.circle),
            child: Icon(icon, color: Colors.white, size: 20),
          ),
          const SizedBox(width: 8),
          Text(label, style: const TextStyle(color: Colors.white, fontWeight: FontWeight.bold)),
        ],
      ),
    );
  }

  Widget _buildQuickTopUp(BuildContext context, WidgetRef ref) {
    final amounts = [10.0, 50.0, 100.0, 200.0];
    return SizedBox(
      height: 60,
      child: ListView.builder(
        padding: const EdgeInsets.symmetric(horizontal: 16),
        scrollDirection: Axis.horizontal,
        itemCount: amounts.length,
        itemBuilder: (context, index) {
          final amount = amounts[index];
          return Padding(
            padding: const EdgeInsets.symmetric(horizontal: 8),
            child: ActionChip(
              label: Text('GHS ${amount.toInt()}'),
              backgroundColor: Colors.white.withOpacity(0.1),
              labelStyle: const TextStyle(color: Colors.white, fontWeight: FontWeight.bold),
              side: BorderSide(color: Colors.white24),
              onPressed: () => _handleTopUp(context, ref, amount),
            ),
          );
        },
      ),
    );
  }

  Widget _buildTransactionList(List<dynamic> txs) {
    return ListView.separated(
      shrinkWrap: true,
      physics: const NeverScrollableScrollPhysics(),
      padding: const EdgeInsets.symmetric(horizontal: 24),
      itemCount: txs.length,
      separatorBuilder: (context, index) => const Divider(height: 1, color: Colors.white12),
      itemBuilder: (context, index) {
        final tx = txs[index];
        final bool isCredit = tx['type'] == 'wallet_topup' || tx['type'] == 'earning';
        final DateTime date = DateTime.parse(tx['created_at']);

        return Padding(
          padding: const EdgeInsets.symmetric(vertical: 16),
          child: Row(
            children: [
              Container(
                padding: const EdgeInsets.all(12),
                decoration: BoxDecoration(
                  color: (isCredit ? const Color(0xFF00D4AA) : const Color(0xFFFF6B6B)).withOpacity(0.15),
                  shape: BoxShape.circle,
                ),
                child: Icon(
                  isCredit ? Icons.add : Icons.remove,
                  color: isCredit ? const Color(0xFF00D4AA) : const Color(0xFFFF6B6B),
                  size: 20,
                ),
              ),
              const SizedBox(width: 16),
              Expanded(
                child: Column(
                  crossAxisAlignment: CrossAxisAlignment.start,
                  children: [
                    Text(_getTransactionTitle(tx['type']), style: const TextStyle(fontWeight: FontWeight.bold, fontSize: 16, color: Colors.white)),
                    Text(DateFormat('MMM dd, yyyy • HH:mm').format(date), style: const TextStyle(color: Colors.white38, fontSize: 12)),
                  ],
                ),
              ),
              Text(
                '${isCredit ? '+' : '-'}GHS ${double.parse(tx['amount']).toStringAsFixed(2)}',
                style: TextStyle(fontWeight: FontWeight.bold, fontSize: 16, color: isCredit ? const Color(0xFF00D4AA) : Colors.white),
              ),
            ],
          ),
        );
      },
    );
  }

  Widget _buildEmptyState() {
    return Center(
      child: Padding(
        padding: const EdgeInsets.all(48.0),
        child: Column(
          children: [
            Icon(Icons.receipt_long_outlined, size: 64, color: Colors.white.withOpacity(0.1)),
            const SizedBox(height: 16),
            const Text('No transactions yet', style: TextStyle(color: Colors.white38)),
          ],
        ),
      ),
    );
  }

  String _getTransactionTitle(String type) {
    switch (type) {
      case 'wallet_topup': return 'Top-up';
      case 'payment': return 'Ride Payment';
      case 'earning': return 'Ride Earnings';
      case 'commission': return 'Platform Fee';
      case 'payout': return 'Withdrawal';
      default: return 'Transaction';
    }
  }

  void _showTopUpDialog(BuildContext context) {
    final controller = TextEditingController();
    showDialog(
      context: context,
      builder: (ctx) => AlertDialog(
        backgroundColor: const Color(0xFF1A1A2E),
        shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(20)),
        title: const Text('Top Up Wallet', style: TextStyle(fontWeight: FontWeight.bold, color: Colors.white)),
        content: TextField(
          controller: controller,
          keyboardType: TextInputType.number,
          style: const TextStyle(color: Colors.white),
          decoration: InputDecoration(
            prefixText: 'GHS ',
            prefixStyle: const TextStyle(color: Colors.white70),
            hintText: 'Enter custom amount',
            hintStyle: const TextStyle(color: Colors.white38),
            filled: true,
            fillColor: Colors.white.withOpacity(0.08),
            border: OutlineInputBorder(borderRadius: BorderRadius.circular(12), borderSide: BorderSide.none),
            enabledBorder: OutlineInputBorder(borderRadius: BorderRadius.circular(12), borderSide: BorderSide(color: Colors.white12)),
            focusedBorder: OutlineInputBorder(borderRadius: BorderRadius.circular(12), borderSide: BorderSide(color: Colors.white24)),
          ),
        ),
        actions: [
          TextButton(onPressed: () => Navigator.pop(ctx), child: const Text('Cancel', style: TextStyle(color: Colors.white54))),
          ElevatedButton(
            style: ElevatedButton.styleFrom(backgroundColor: Colors.white, foregroundColor: Colors.black),
            onPressed: () {
              final amount = double.tryParse(controller.text);
              if (amount != null && amount > 0) {
                Navigator.pop(ctx);
                _handleTopUp(context, (ctx as Element).findAncestorWidgetOfExactType<Consumer>() != null
                    ? (ctx as dynamic).ref
                    : null, amount);
              }
            },
            child: const Text('Proceed', style: TextStyle(fontWeight: FontWeight.bold)),
          ),
        ],
      ),
    );
  }

  void _showStatement(BuildContext context, WalletState state) {
    showModalBottomSheet(
      context: context,
      isScrollControlled: true,
      backgroundColor: AppColors.obsidianDark,
      shape: const RoundedRectangleBorder(borderRadius: BorderRadius.vertical(top: Radius.circular(24))),
      builder: (ctx) => DraggableScrollableSheet(
        initialChildSize: 0.6,
        maxChildSize: 0.95,
        minChildSize: 0.4,
        expand: false,
        builder: (_, scrollController) {
          final txs = state.transactions;
          return SingleChildScrollView(
            controller: scrollController,
            padding: const EdgeInsets.fromLTRB(24, 20, 24, 40),
            child: Column(
              crossAxisAlignment: CrossAxisAlignment.start,
              children: [
                Center(child: Container(width: 40, height: 4, decoration: BoxDecoration(color: Colors.white24, borderRadius: BorderRadius.circular(2)))),
                const SizedBox(height: 20),
                const Text('Account Statement', style: TextStyle(fontSize: 22, fontWeight: FontWeight.bold, color: Colors.white)),
                const SizedBox(height: 4),
                Text('Balance: ${state.currency} ${state.balance.toStringAsFixed(2)}', style: const TextStyle(color: Colors.white54)),
                const SizedBox(height: 24),
                if (txs.isEmpty)
                  const Center(
                    child: Padding(
                      padding: EdgeInsets.all(40),
                      child: Text('No transactions to display.', style: TextStyle(color: Colors.white38)),
                    ),
                  )
                else
                  ...txs.map((tx) {
                    final isCredit = tx['type'] == 'wallet_topup' || tx['type'] == 'earning';
                    return ListTile(
                      contentPadding: EdgeInsets.zero,
                      leading: CircleAvatar(
                        backgroundColor: (isCredit ? const Color(0xFF00D4AA) : const Color(0xFFFF6B6B)).withOpacity(0.15),
                        child: Icon(isCredit ? Icons.add : Icons.remove, color: isCredit ? const Color(0xFF00D4AA) : const Color(0xFFFF6B6B), size: 18),
                      ),
                      title: Text(_getTransactionTitle(tx['type']), style: const TextStyle(fontWeight: FontWeight.w600, color: Colors.white)),
                      subtitle: Text(tx['created_at'] ?? '', style: const TextStyle(fontSize: 12, color: Colors.white38)),
                      trailing: Text(
                        '${isCredit ? '+' : '-'}${state.currency} ${double.tryParse(tx['amount'].toString())?.toStringAsFixed(2) ?? '0.00'}',
                        style: TextStyle(fontWeight: FontWeight.bold, color: isCredit ? const Color(0xFF00D4AA) : const Color(0xFFFF6B6B)),
                      ),
                    );
                  }),
              ],
            ),
          );
        },
      ),
    );
  }

  Future<void> _handleTopUp(BuildContext context, WidgetRef ref, double amount) async {
    final url = await ref.read(walletProvider.notifier).initializeTopUp(amount);
    if (url != null && context.mounted) {
      Navigator.push(context, MaterialPageRoute(
        builder: (_) => TopUpWebview(url: url),
      ));
    }
  }
}

class TopUpWebview extends StatefulWidget {
  final String url;
  const TopUpWebview({super.key, required this.url});

  @override
  State<TopUpWebview> createState() => _TopUpWebviewState();
}

class _TopUpWebviewState extends State<TopUpWebview> {
  late final WebViewController _controller;

  @override
  void initState() {
    super.initState();
    _controller = WebViewController()
      ..setJavaScriptMode(JavaScriptMode.unrestricted)
      ..setNavigationDelegate(
        NavigationDelegate(
          onNavigationRequest: (NavigationRequest request) {
            if (request.url.contains('/wallet/verify')) {
              final uri = Uri.parse(request.url);
              final reference = uri.queryParameters['reference'];
              if (reference != null) {
                _completeTopUp(reference);
              }
              return NavigationDecision.prevent;
            }
            return NavigationDecision.navigate;
          },
        ),
      )
      ..loadRequest(Uri.parse(widget.url));
  }

  void _completeTopUp(String reference) {
    Navigator.pop(context, reference);
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      backgroundColor: AppColors.obsidianDark,
      appBar: AppBar(
        title: const Text('Top Up Wallet', style: TextStyle(color: Colors.white)),
        backgroundColor: Colors.transparent,
        elevation: 0,
        foregroundColor: Colors.white,
      ),
      body: WebViewWidget(controller: _controller),
    );
  }
}
