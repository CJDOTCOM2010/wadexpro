import 'package:flutter/material.dart';
import 'package:flutter_riverpod/flutter_riverpod.dart';
import '../../../../core/theme/app_colors.dart';
import 'package:wadexpro_customer/features/auth/presentation/providers/auth_provider.dart';
import 'package:wadexpro_customer/features/wallet/presentation/providers/wallet_provider.dart';
import 'package:wadexpro_customer/features/wallet/presentation/pages/wallet_screen.dart';
import '../../../../core/models/user_model.dart';

class AccountTab extends ConsumerWidget {
  const AccountTab({super.key});

  @override
  Widget build(BuildContext context, WidgetRef ref) {
    final authState = ref.watch(authProvider);
    final walletState = ref.watch(walletProvider);
    final user = authState.user;

    return Scaffold(
      backgroundColor: Colors.white,
      body: SafeArea(
        child: SingleChildScrollView(
          padding: const EdgeInsets.symmetric(horizontal: 20),
          child: Column(
            crossAxisAlignment: CrossAxisAlignment.start,
            children: [
              const SizedBox(height: 20),
              _buildHeader(user),
              const SizedBox(height: 32),
              _buildWalletCard(context, walletState),
              const SizedBox(height: 32),
              _buildAccountOptions(context, ref),
              const SizedBox(height: 48),
              _buildLogoutButton(ref),
              const SizedBox(height: 40),
            ],
          ),
        ),
      ),
    );
  }

  Widget _buildHeader(UserModel? user) {
    return Row(
      mainAxisAlignment: MainAxisAlignment.spaceBetween,
      children: [
        Column(
          crossAxisAlignment: CrossAxisAlignment.start,
          children: [
            Text(
              user?.name ?? 'WADEXPRO User',
              style: const TextStyle(fontSize: 32, fontWeight: FontWeight.bold),
            ),
            const SizedBox(height: 4),
            Row(
              children: [
                Container(
                  padding: const EdgeInsets.symmetric(horizontal: 8, vertical: 4),
                  decoration: BoxDecoration(
                    color: Colors.grey.shade100,
                    borderRadius: BorderRadius.circular(12),
                  ),
                  child: const Row(
                    children: [
                      Icon(Icons.star, size: 14, color: Colors.black),
                      SizedBox(width: 4),
                      Text('5.0', style: TextStyle(fontWeight: FontWeight.bold, fontSize: 12)),
                    ],
                  ),
                ),
              ],
            ),
          ],
        ),
        CircleAvatar(
          radius: 36,
          backgroundColor: Colors.grey.shade200,
          child: const Icon(Icons.person, size: 40, color: Colors.grey),
        ),
      ],
    );
  }

  Widget _buildWalletCard(BuildContext context, WalletState state) {
    return InkWell(
      onTap: () {
        Navigator.of(context).push(
          MaterialPageRoute(builder: (_) => const WalletScreen()),
        );
      },
      child: Container(
        padding: const EdgeInsets.all(24),
        decoration: BoxDecoration(
          color: AppColors.primary,
          borderRadius: BorderRadius.circular(20),
          boxShadow: [
            BoxShadow(
              color: AppColors.primary.withOpacity(0.2),
              blurRadius: 15,
              offset: const Offset(0, 8),
            )
          ],
        ),
        child: Column(
          children: [
            Row(
              mainAxisAlignment: MainAxisAlignment.spaceBetween,
              children: [
                const Text('Wallet Balance', style: TextStyle(color: Colors.white70, fontSize: 15)),
                const Icon(Icons.account_balance_wallet_outlined, color: Colors.white70, size: 20),
              ],
            ),
            const SizedBox(height: 8),
            Row(
              mainAxisAlignment: MainAxisAlignment.spaceBetween,
              children: [
                Text(
                  '${state.currency} ${state.balance.toStringAsFixed(2)}',
                  style: const TextStyle(color: Colors.white, fontSize: 24, fontWeight: FontWeight.bold),
                ),
                const Icon(Icons.arrow_forward_ios, color: Colors.white, size: 16),
              ],
            ),
          ],
        ),
      ),
    );
  }

  Widget _buildAccountOptions(BuildContext context, WidgetRef ref) {
    return Column(
      children: [
        _buildOptionTile(Icons.message, 'Messages', () {}),
        _buildOptionTile(Icons.settings, 'Settings', () {}),
        _buildOptionTile(Icons.payment, 'Manage Payment Methods', () {}),
        _buildOptionTile(Icons.support_agent, 'Support', () {}),
        _buildOptionTile(Icons.info_outline, 'Legal', () {}),
      ],
    );
  }

  Widget _buildOptionTile(IconData icon, String title, VoidCallback onTap) {
    return ListTile(
      onTap: onTap,
      contentPadding: EdgeInsets.zero,
      leading: Icon(icon, color: Colors.black, size: 26),
      title: Text(title, style: const TextStyle(fontWeight: FontWeight.w600, fontSize: 16)),
      trailing: const Icon(Icons.arrow_forward_ios, size: 14, color: Colors.grey),
    );
  }

  Widget _buildLogoutButton(WidgetRef ref) {
    return Center(
      child: TextButton(
        onPressed: () => ref.read(authProvider.notifier).logout(),
        child: const Text('Log Out', style: TextStyle(color: Colors.red, fontWeight: FontWeight.bold, fontSize: 16)),
      ),
    );
  }
}
