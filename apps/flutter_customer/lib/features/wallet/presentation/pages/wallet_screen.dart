import 'package:flutter/material.dart';
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
      backgroundColor: AppColors.background,
      appBar: AppBar(
        title: const Text('WADEXP Wallet', style: TextStyle(fontWeight: FontWeight.bold)),
        centerTitle: true,
        backgroundColor: Colors.white,
        elevation: 0,
        foregroundColor: AppColors.primary,
        actions: [
          IconButton(
            onPressed: () => ref.read(walletProvider.notifier).refresh(),
            icon: const Icon(Icons.refresh),
          ),
        ],
      ),
      body: RefreshIndicator(
        onRefresh: () => ref.read(walletProvider.notifier).refresh(),
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
              const Padding(
                padding: EdgeInsets.fromLTRB(24, 32, 24, 16),
                child: Text(
                  'Quick Top-up',
                  style: TextStyle(fontSize: 18, fontWeight: FontWeight.bold, color: AppColors.primary),
                ),
              ),
              _buildQuickTopUp(context, ref),
              _buildInStoreAgentCta(context),
              const Padding(
                padding: EdgeInsets.fromLTRB(24, 32, 24, 16),
                child: Text(
                  'Recent Activity',
                  style: TextStyle(fontSize: 18, fontWeight: FontWeight.bold, color: AppColors.primary),
                ),
              ),
              if (walletState.isLoading && walletState.transactions.isEmpty)
                const Center(child: Padding(padding: EdgeInsets.all(40), child: CircularProgressIndicator()))
              else if (walletState.transactions.isEmpty)
                _buildEmptyState()
              else
                _buildTransactionList(walletState.transactions),
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
          const Text('Trip Profiles', style: TextStyle(fontWeight: FontWeight.bold, fontSize: 18, color: AppColors.primary)),
          const SizedBox(height: 12),
          Container(
            padding: const EdgeInsets.all(16),
            decoration: BoxDecoration(
              color: Colors.white,
              borderRadius: BorderRadius.circular(20),
              border: Border.all(color: AppColors.primary.withOpacity(0.05)),
            ),
            child: Row(
              children: [
                const Icon(Icons.person, color: AppColors.primary),
                const SizedBox(width: 16),
                const Expanded(
                  child: Column(
                    crossAxisAlignment: CrossAxisAlignment.start,
                    children: [
                      Text('Personal Profile', style: TextStyle(fontWeight: FontWeight.bold)),
                      Text('Default individual billing', style: TextStyle(fontSize: 12, color: AppColors.textMuted)),
                    ],
                  ),
                ),
                Container(
                  padding: const EdgeInsets.symmetric(horizontal: 8, vertical: 4),
                  decoration: BoxDecoration(color: Colors.green.withOpacity(0.1), borderRadius: BorderRadius.circular(8)),
                  child: const Text('ACTIVE', style: TextStyle(fontSize: 10, fontWeight: FontWeight.bold, color: Colors.green)),
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
          const Row(
            mainAxisAlignment: MainAxisAlignment.spaceBetween,
            children: [
              Text('Payment Methods', style: TextStyle(fontWeight: FontWeight.bold, fontSize: 18, color: AppColors.primary)),
              Icon(Icons.add_circle_outline, color: AppColors.primary, size: 20),
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
        color: AppColors.primary.withOpacity(0.02),
        borderRadius: BorderRadius.circular(20),
        border: Border.all(color: AppColors.primary.withOpacity(0.05), style: BorderStyle.solid),
      ),
      child: const Column(
        children: [
          Icon(Icons.credit_card, color: AppColors.textMuted),
          SizedBox(height: 8),
          Text('No cards or accounts linked', style: TextStyle(color: AppColors.textMuted, fontSize: 12)),
        ],
      ),
    );
  }

  Widget _buildPaymentMethodTile(dynamic pm) {
    return ListTile(
      contentPadding: EdgeInsets.zero,
      leading: Icon(pm['provider'] == 'CARD' ? Icons.credit_card : Icons.wallet_membership, color: AppColors.primary),
      title: Text('${pm['brand'] ?? pm['provider']} •••• ${pm['last_four'] ?? ''}'),
      trailing: pm['is_default'] ? const Icon(Icons.check_circle, color: Colors.green) : null,
    );
  }

  Widget _buildPromotionsSection(BuildContext context, WidgetRef ref) {
    final controller = TextEditingController();
    return Padding(
      padding: const EdgeInsets.fromLTRB(24, 32, 24, 0),
      child: Column(
        crossAxisAlignment: CrossAxisAlignment.start,
        children: [
          const Text('Promotions & Vouchers', style: TextStyle(fontWeight: FontWeight.bold, fontSize: 18, color: AppColors.primary)),
          const SizedBox(height: 12),
          TextField(
            controller: controller,
            decoration: InputDecoration(
              hintText: 'Enter Promo Code',
              filled: true,
              fillColor: Colors.white,
              border: OutlineInputBorder(borderRadius: BorderRadius.circular(16), borderSide: BorderSide.none),
              suffixIcon: IconButton(
                onPressed: () async {
                   final success = await ref.read(walletProvider.notifier).redeemPromo(controller.text);
                   if (context.mounted) {
                     ScaffoldMessenger.of(context).showSnackBar(SnackBar(
                       content: Text(success ? 'Promo Applied!' : 'Invalid Promo Code'),
                       backgroundColor: success ? Colors.green : Colors.red,
                     ));
                   }
                },
                icon: const Icon(Icons.arrow_forward, color: AppColors.primary),
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
        color: AppColors.primary,
        borderRadius: BorderRadius.circular(24),
        boxShadow: [BoxShadow(color: AppColors.primary.withOpacity(0.2), blurRadius: 15, offset: const Offset(0, 8))],
      ),
      child: Column(
        children: [
          const Row(
            children: [
              Icon(Icons.stars, color: Colors.amber, size: 28),
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
            decoration: BoxDecoration(color: Colors.white.withOpacity(0.1), borderRadius: BorderRadius.circular(12)),
            child: Row(
              mainAxisAlignment: MainAxisAlignment.spaceBetween,
              children: [
                Text(state.referralCode ?? 'WADEXPRO', style: const TextStyle(color: Colors.white, fontWeight: FontWeight.bold, letterSpacing: 2)),
                const Icon(Icons.copy, color: Colors.white, size: 18),
              ],
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
        color: Colors.white,
        borderRadius: BorderRadius.circular(20),
        border: Border.all(color: AppColors.primary.withOpacity(0.1)),
      ),
      child: Row(
        children: [
          Container(
            padding: const EdgeInsets.all(12),
            decoration: BoxDecoration(color: AppColors.primary.withOpacity(0.05), shape: BoxShape.circle),
            child: const Icon(Icons.storefront, color: AppColors.primary),
          ),
          const SizedBox(width: 16),
          const Expanded(
            child: Column(
              crossAxisAlignment: CrossAxisAlignment.start,
              children: [
                Text('In-Store Top-up', style: TextStyle(fontWeight: FontWeight.bold)),
                Text('Visit any WADEX Office to top up via Cash.', style: TextStyle(fontSize: 11, color: AppColors.textMuted)),
              ],
            ),
          ),
          const Icon(Icons.chevron_right, color: AppColors.textMuted),
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
        gradient: AppColors.luxuryGradient,
        borderRadius: BorderRadius.circular(28),
        boxShadow: [
          BoxShadow(
            color: AppColors.primary.withOpacity(0.3),
            blurRadius: 20,
            offset: const Offset(0, 10),
          )
        ],
      ),
      child: Column(
        crossAxisAlignment: CrossAxisAlignment.start,
        children: [
          Row(
            mainAxisAlignment: MainAxisAlignment.spaceBetween,
            children: [
              const Text(
                'Available Balance',
                style: TextStyle(color: Colors.white70, fontSize: 16),
              ),
              Image.network(
                'https://upload.wikimedia.org/wikipedia/commons/thumb/b/b5/PayPal.svg/1200px-PayPal.svg.png', // Placeholder for card network logo
                height: 20,
                color: Colors.white24,
                errorBuilder: (context, error, stackTrace) => const Icon(Icons.wallet, color: Colors.white24),
              ),
            ],
          ),
          const SizedBox(height: 12),
          Text(
            '${state.currency} ${state.balance.toStringAsFixed(2)}',
            style: const TextStyle(
              color: Colors.white,
              fontSize: 36,
              fontWeight: FontWeight.w900,
              letterSpacing: 1.2,
            ),
          ),
          const SizedBox(height: 32),
          Row(
            children: [
              _buildCardAction(Icons.add, 'Top Up', () => _showTopUpDialog(context)),
              const SizedBox(width: 24),
              _buildCardAction(Icons.history, 'Statement', () {}),
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
            decoration: BoxDecoration(
              color: Colors.white.withOpacity(0.2),
              shape: BoxShape.circle,
            ),
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
              backgroundColor: Colors.white,
              labelStyle: const TextStyle(color: AppColors.primary, fontWeight: FontWeight.bold),
              side: BorderSide(color: AppColors.primary.withOpacity(0.1)),
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
      separatorBuilder: (context, index) => const Divider(height: 1, color: Colors.black12),
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
                  color: (isCredit ? Colors.green : Colors.red).withOpacity(0.1),
                  shape: BoxShape.circle,
                ),
                child: Icon(
                  isCredit ? Icons.add : Icons.remove,
                  color: isCredit ? Colors.green : Colors.red,
                  size: 20,
                ),
              ),
              const SizedBox(width: 16),
              Expanded(
                child: Column(
                  crossAxisAlignment: CrossAxisAlignment.start,
                  children: [
                    Text(
                      _getTransactionTitle(tx['type']),
                      style: const TextStyle(fontWeight: FontWeight.bold, fontSize: 16),
                    ),
                    Text(
                      DateFormat('MMM dd, yyyy • HH:mm').format(date),
                      style: const TextStyle(color: AppColors.textMuted, fontSize: 12),
                    ),
                  ],
                ),
              ),
              Text(
                '${isCredit ? '+' : '-'}GHS ${double.parse(tx['amount']).toStringAsFixed(2)}',
                style: TextStyle(
                  fontWeight: FontWeight.bold,
                  fontSize: 16,
                  color: isCredit ? Colors.green : AppColors.textBody,
                ),
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
            Icon(Icons.receipt_long_outlined, size: 64, color: AppColors.primary.withOpacity(0.1)),
            const SizedBox(height: 16),
            const Text('No transactions yet', style: TextStyle(color: AppColors.textMuted)),
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
    // Basic dialog to input custom amount could go here
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
              // Extract reference and verify
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
    // This is a bit tricky with nested context, better to use ref if possible
    // But for a simple demo, we pop and the user can refresh or we trigger it via provider
    Navigator.pop(context, reference);
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      appBar: AppBar(title: const Text('Top Up Wallet')),
      body: WebViewWidget(controller: _controller),
    );
  }
}
