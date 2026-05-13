import 'dart:io';
import 'package:flutter/material.dart';
import 'package:flutter/services.dart';
import 'package:url_launcher/url_launcher.dart';
import 'package:flutter_riverpod/flutter_riverpod.dart';
import 'package:flutter_animate/flutter_animate.dart';
import 'package:image_picker/image_picker.dart';
import 'package:image_cropper/image_cropper.dart';
import '../../../../core/theme/app_colors.dart';
import '../../../../core/theme/theme_provider.dart';
import 'package:wadexpro_customer/features/auth/presentation/providers/auth_provider.dart';
import 'package:wadexpro_customer/features/wallet/presentation/providers/wallet_provider.dart';
import 'package:wadexpro_customer/features/wallet/presentation/pages/wallet_screen.dart';
import '../../../../core/models/user_model.dart';
import '../../../../core/providers/settings_provider.dart';
import '../../../../core/providers/dashboard_provider.dart';
import '../widgets/appearance_modal.dart';
import 'wadex_account_screen.dart';
import '../../chat/presentation/pages/support_chat_screen.dart';

class AccountTab extends ConsumerStatefulWidget {
  const AccountTab({super.key});

  @override
  ConsumerState<AccountTab> createState() => _AccountTabState();
}

class _AccountTabState extends ConsumerState<AccountTab> {
  final ImagePicker _picker = ImagePicker();

  @override
  void initState() {
    super.initState();
    WidgetsBinding.instance.addPostFrameCallback((_) {
      ref.read(authProvider.notifier).fetchProfile();
    });
  }

  Future<void> _updateProfileImage() async {
    final ImageSource? source = await showModalBottomSheet<ImageSource>(
      context: context,
      backgroundColor: const Color(0xFF1A1A2E),
      shape: const RoundedRectangleBorder(borderRadius: BorderRadius.vertical(top: Radius.circular(30))),
      builder: (ctx) => Container(
        padding: const EdgeInsets.all(32),
        child: Column(
          mainAxisSize: MainAxisSize.min,
          children: [
            const Text('Update Photo', style: TextStyle(color: Colors.white, fontSize: 20, fontWeight: FontWeight.bold)),
            const SizedBox(height: 24),
            ListTile(
              leading: Container(
                padding: const EdgeInsets.all(10),
                decoration: BoxDecoration(color: const Color(0xFF74B9FF).withOpacity(0.1), shape: BoxShape.circle),
                child: const Icon(Icons.photo_library_rounded, color: Color(0xFF74B9FF)),
              ),
              title: const Text('Choose from Gallery', style: TextStyle(color: Colors.white)),
              onTap: () => Navigator.pop(ctx, ImageSource.gallery),
            ),
            const SizedBox(height: 12),
            ListTile(
              leading: Container(
                padding: const EdgeInsets.all(10),
                decoration: BoxDecoration(color: const Color(0xFF00D4AA).withOpacity(0.1), shape: BoxShape.circle),
                child: const Icon(Icons.camera_alt_rounded, color: Color(0xFF00D4AA)),
              ),
              title: const Text('Take a Photo', style: TextStyle(color: Colors.white)),
              onTap: () => Navigator.pop(ctx, ImageSource.camera),
            ),
          ],
        ),
      ),
    );

    if (source == null) return;

    final XFile? image = await _picker.pickImage(source: source);
    if (image == null) return;

    final croppedFile = await ImageCropper().cropImage(
      sourcePath: image.path,
      aspectRatio: CropAspectRatio(ratioX: 1, ratioY: 1),
      compressQuality: 90,
      maxWidth: 600,
      maxHeight: 600,
      uiSettings: [
        AndroidUiSettings(
          toolbarTitle: 'Crop Profile Picture',
          toolbarColor: AppColors.obsidianDark,
          toolbarWidgetColor: Colors.white,
          initAspectRatio: CropAspectRatioPreset.square,
          lockAspectRatio: true,
          activeControlsWidgetColor: const Color(0xFF6C63FF),
        ),
        IOSUiSettings(
          title: 'Crop Profile Picture',
          aspectRatioLockEnabled: true,
        ),
      ],
    );

    if (croppedFile != null) {
      final success = await ref.read(authProvider.notifier).updateProfileImage(croppedFile.path);
      
      if (!mounted) return;
      ScaffoldMessenger.of(context).showSnackBar(
        SnackBar(
          content: Text(success ? 'Profile picture updated successfully' : 'Failed to update profile picture'),
          backgroundColor: success ? const Color(0xFF00D4AA) : const Color(0xFFFF6B6B),
          behavior: SnackBarBehavior.floating,
          shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(12)),
        ),
      );
    }
  }

  @override
  Widget build(BuildContext context) {
    final authState = ref.watch(authProvider);
    final walletState = ref.watch(walletProvider);
    final user = authState.user;

    return Scaffold(
      backgroundColor: AppColors.obsidianDark,
      body: CustomScrollView(
        physics: const BouncingScrollPhysics(),
        slivers: [
          _buildUltraPremiumHeader(context, user),
          SliverToBoxAdapter(
            child: Padding(
              padding: const EdgeInsets.symmetric(horizontal: 20),
              child: Column(
                crossAxisAlignment: CrossAxisAlignment.start,
                children: [
                  const SizedBox(height: 12),
                  _buildMembershipCard(user),
                  const SizedBox(height: 32),
                  _buildStatsRow(),
                  const SizedBox(height: 32),
                  _buildWalletGlassCard(context, walletState),
                  const SizedBox(height: 32),
                  _buildSectionHeader('Account'),
                  const SizedBox(height: 16),
                  _buildAccountSection(context, user),
                  const SizedBox(height: 32),
                  _buildSectionHeader('Management'),
                  const SizedBox(height: 16),
                  _buildActionGrid(context, ref),
                  const SizedBox(height: 32),
                  _buildSectionHeader('Preferences'),
                  const SizedBox(height: 16),
                  _buildPremiumSettings(context, ref),
                  const SizedBox(height: 32),
                  _buildSectionHeader('Security & Legal'),
                  const SizedBox(height: 16),
                  _buildSecurityCard(context),
                  const SizedBox(height: 48),
                  _buildPremiumLogout(ref, context),
                  const SizedBox(height: 120),
                ],
              ),
            ),
          ),
        ],
      ),
    );
  }

  Widget _buildUltraPremiumHeader(BuildContext context, UserModel? user) {
    return SliverAppBar(
      expandedHeight: 280,
      pinned: true,
      backgroundColor: AppColors.obsidianDark,
      elevation: 0,
      flexibleSpace: FlexibleSpaceBar(
        background: Stack(
          fit: StackFit.expand,
          children: [
            // Background Image/Gradient
            Container(
              decoration: BoxDecoration(
                gradient: LinearGradient(
                  begin: Alignment.topCenter,
                  end: Alignment.bottomCenter,
                  colors: [
                    const Color(0xFF6C63FF).withOpacity(0.2),
                    AppColors.obsidianDark,
                  ],
                ),
              ),
            ),
            
            // Decorative elements
            Positioned(
              top: -80,
              right: -80,
              child: Container(
                width: 300,
                height: 300,
                decoration: BoxDecoration(
                  shape: BoxShape.circle,
                  color: const Color(0xFF6C63FF).withOpacity(0.08),
                ),
              ),
            ).animate().scale(duration: 2000.ms, curve: Curves.easeInOut).fadeIn(),

            // Content
            Column(
              mainAxisAlignment: MainAxisAlignment.center,
              children: [
                const SizedBox(height: 60),
                Stack(
                  alignment: Alignment.bottomRight,
                  children: [
                    GestureDetector(
                      onTap: _updateProfileImage,
                      child: Container(
                        padding: const EdgeInsets.all(5),
                        decoration: BoxDecoration(
                          shape: BoxShape.circle,
                          border: Border.all(color: const Color(0xFF6C63FF).withOpacity(0.3), width: 2),
                          boxShadow: [
                            BoxShadow(color: const Color(0xFF6C63FF).withOpacity(0.2), blurRadius: 30, spreadRadius: 5),
                          ],
                        ),
                        child: CircleAvatar(
                          radius: 55,
                          backgroundColor: AppColors.obsidianDark,
                          backgroundImage: user?.avatarUrl != null 
                            ? (user!.avatarUrl!.startsWith('http') 
                                ? NetworkImage(user!.avatarUrl!) 
                                : FileImage(File(user!.avatarUrl!)) as ImageProvider)
                            : null,
                          child: user?.avatarUrl == null ? const Icon(Icons.person, size: 50, color: Colors.white54) : null,
                        ),
                      ),
                    ),
                    Container(
                      padding: const EdgeInsets.all(8),
                      decoration: const BoxDecoration(
                        color: Color(0xFF6C63FF),
                        shape: BoxShape.circle,
                      ),
                      child: const Icon(Icons.camera_alt, color: Colors.white, size: 18),
                    ).animate().scale(delay: 500.ms, duration: 400.ms, curve: Curves.easeOutBack),
                  ],
                ),
                const SizedBox(height: 16),
                Text(
                  user?.name ?? 'WADEXPRO Elite',
                  style: const TextStyle(fontSize: 28, fontWeight: FontWeight.w900, color: Colors.white, letterSpacing: -0.5),
                ).animate().fadeIn(delay: 200.ms).slideY(begin: 0.2),
                const SizedBox(height: 4),
                Row(
                  mainAxisAlignment: MainAxisAlignment.center,
                  children: [
                    const Icon(Icons.verified, color: Color(0xFF00D4AA), size: 16),
                    const SizedBox(width: 4),
                    Text(
                      'DIAMOND TIER MEMBER',
                      style: TextStyle(
                        color: const Color(0xFF00D4AA), 
                        fontSize: 12, 
                        fontWeight: FontWeight.w900, 
                        letterSpacing: 1.5,
                      ),
                    ),
                  ],
                ).animate().fadeIn(delay: 400.ms),
              ],
            ),
          ],
        ),
      ),
    );
  }

  Widget _buildMembershipCard(UserModel? user) {
    return Container(
      width: double.infinity,
      padding: const EdgeInsets.all(20),
      decoration: BoxDecoration(
        gradient: const LinearGradient(
          colors: [Color(0xFF1E1E30), Color(0xFF2D3436)],
          begin: Alignment.topLeft,
          end: Alignment.bottomRight,
        ),
        borderRadius: BorderRadius.circular(24),
        border: Border.all(color: Colors.white.withOpacity(0.05)),
        boxShadow: [
          BoxShadow(color: Colors.black.withOpacity(0.3), blurRadius: 15, offset: const Offset(0, 10)),
        ],
      ),
      child: Column(
        crossAxisAlignment: CrossAxisAlignment.start,
        children: [
          Row(
            mainAxisAlignment: MainAxisAlignment.spaceBetween,
            children: [
              const Text('REWARDS PROGRESS', style: TextStyle(color: Colors.white38, fontSize: 10, fontWeight: FontWeight.w900, letterSpacing: 1)),
              Text('LVL 12', style: TextStyle(color: const Color(0xFFF9CA24).withOpacity(0.8), fontSize: 12, fontWeight: FontWeight.w900)),
            ],
          ),
          const SizedBox(height: 12),
          Row(
            children: [
              Expanded(
                child: ClipRRect(
                  borderRadius: BorderRadius.circular(10),
                  child: LinearProgressIndicator(
                    value: 0.75,
                    backgroundColor: Colors.white.withOpacity(0.05),
                    color: const Color(0xFF6C63FF),
                    minHeight: 8,
                  ),
                ),
              ),
              const SizedBox(width: 12),
              const Text('75%', style: TextStyle(color: Colors.white, fontSize: 12, fontWeight: FontWeight.bold)),
            ],
          ),
          const SizedBox(height: 12),
          Text(
            'Spend ₵250.00 more to unlock Platinum benefits',
            style: TextStyle(color: Colors.white.withOpacity(0.4), fontSize: 12, fontWeight: FontWeight.w500),
          ),
        ],
      ),
    ).animate().fadeIn(delay: 600.ms).slideX(begin: -0.1);
  }

  Widget _buildStatsRow() {
    return Row(
      children: [
        _buildStatBox('142', 'Trips', const Color(0xFF74B9FF)),
        const SizedBox(width: 16),
        _buildStatBox('₵1.2k', 'Saved', const Color(0xFF00D4AA)),
        const SizedBox(width: 16),
        _buildStatBox('4.9', 'Rating', const Color(0xFFF9CA24)),
      ],
    ).animate().fadeIn(delay: 800.ms).slideY(begin: 0.1);
  }

  Widget _buildStatBox(String value, String label, Color color) {
    return Expanded(
      child: Container(
        padding: const EdgeInsets.symmetric(vertical: 16),
        decoration: BoxDecoration(
          color: Colors.white.withOpacity(0.03),
          borderRadius: BorderRadius.circular(20),
          border: Border.all(color: Colors.white.withOpacity(0.05)),
        ),
        child: Column(
          children: [
            Text(value, style: const TextStyle(color: Colors.white, fontSize: 20, fontWeight: FontWeight.w900)),
            const SizedBox(height: 4),
            Text(label, style: TextStyle(color: Colors.white.withOpacity(0.4), fontSize: 12, fontWeight: FontWeight.bold)),
          ],
        ),
      ),
    );
  }

  Widget _buildWalletGlassCard(BuildContext context, WalletState state) {
    return Container(
      decoration: BoxDecoration(
        gradient: const LinearGradient(
          colors: [Color(0xFF6C63FF), Color(0xFF4834DF)],
          begin: Alignment.topLeft,
          end: Alignment.bottomRight,
        ),
        borderRadius: BorderRadius.circular(30),
        boxShadow: [
          BoxShadow(color: const Color(0xFF6C63FF).withOpacity(0.3), blurRadius: 25, offset: const Offset(0, 10)),
        ],
      ),
      child: Material(
        color: Colors.transparent,
        child: InkWell(
          onTap: () => Navigator.of(context).push(MaterialPageRoute(builder: (_) => const WalletScreen())),
          borderRadius: BorderRadius.circular(30),
          child: Padding(
            padding: const EdgeInsets.all(24),
            child: Column(
              children: [
                Row(
                  mainAxisAlignment: MainAxisAlignment.spaceBetween,
                  children: [
                    const Text('TOTAL ASSETS', style: TextStyle(color: Colors.white60, fontSize: 12, fontWeight: FontWeight.w900, letterSpacing: 1)),
                    Container(
                      padding: const EdgeInsets.all(6),
                      decoration: BoxDecoration(color: Colors.white.withOpacity(0.15), shape: BoxShape.circle),
                      child: const Icon(Icons.account_balance_wallet, color: Colors.white, size: 18),
                    ),
                  ],
                ),
                const SizedBox(height: 12),
                Row(
                  crossAxisAlignment: CrossAxisAlignment.end,
                  children: [
                    Text(
                      '${state.currency} ${state.balance.toStringAsFixed(2)}',
                      style: const TextStyle(color: Colors.white, fontSize: 36, fontWeight: FontWeight.w900, letterSpacing: -1),
                    ),
                  ],
                ),
                const SizedBox(height: 24),
                Row(
                  children: [
                    Expanded(
                      child: Container(
                        padding: const EdgeInsets.symmetric(vertical: 12),
                        decoration: BoxDecoration(color: Colors.white.withOpacity(0.15), borderRadius: BorderRadius.circular(16)),
                        child: const Center(child: Text('Add Money', style: TextStyle(color: Colors.white, fontWeight: FontWeight.bold))),
                      ),
                    ),
                    const SizedBox(width: 12),
                    Expanded(
                      child: Container(
                        padding: const EdgeInsets.symmetric(vertical: 12),
                        decoration: BoxDecoration(color: Colors.white.withOpacity(0.1), borderRadius: BorderRadius.circular(16)),
                        child: const Center(child: Text('History', style: TextStyle(color: Colors.white70, fontWeight: FontWeight.bold))),
                      ),
                    ),
                  ],
                ),
              ],
            ),
          ),
        ),
      ),
    );
  }

  Widget _buildSectionHeader(String title) {
    return Row(
      children: [
        Container(width: 4, height: 16, decoration: BoxDecoration(color: const Color(0xFF6C63FF), borderRadius: BorderRadius.circular(2))),
        const SizedBox(width: 12),
        Text(
          title.toUpperCase(),
          style: const TextStyle(fontSize: 14, fontWeight: FontWeight.w900, color: Colors.white, letterSpacing: 1.5),
        ),
      ],
    );
  }

  Widget _buildActionGrid(BuildContext context, WidgetRef ref) {
    return GridView.count(
      shrinkWrap: true,
      physics: const NeverScrollableScrollPhysics(),
      crossAxisCount: 2,
      mainAxisSpacing: 16,
      crossAxisSpacing: 16,
      childAspectRatio: 1.6,
      children: [
        _buildGridAction(Icons.history_edu_rounded, 'Activity', 'Recent trips', const Color(0xFF74B9FF), () {
          ref.read(dashboardIndexProvider.notifier).state = 2;
        }),
        _buildGridAction(Icons.auto_awesome_rounded, 'Rewards', 'View benefits', const Color(0xFFF9CA24), () => _showComingSoon(context, 'Rewards Program')),
        _buildGridAction(Icons.qr_code_2_rounded, 'ID Card', 'Member pass', const Color(0xFF00D4AA), () => _showComingSoon(context, 'Virtual ID')),
        _buildGridAction(Icons.group_add_rounded, 'Refer', 'Earn ₵20.00', const Color(0xFFFF6B6B), () => _showComingSoon(context, 'Referrals')),
      ],
    );
  }

  Widget _buildGridAction(IconData icon, String title, String sub, Color color, VoidCallback onTap) {
    return InkWell(
      onTap: onTap,
      borderRadius: BorderRadius.circular(24),
      child: Container(
        padding: const EdgeInsets.all(16),
        decoration: BoxDecoration(
          color: Colors.white.withOpacity(0.03),
          borderRadius: BorderRadius.circular(24),
          border: Border.all(color: Colors.white.withOpacity(0.05)),
        ),
        child: Column(
          crossAxisAlignment: CrossAxisAlignment.start,
          children: [
            Icon(icon, color: color, size: 28),
            const Spacer(),
            Text(title, style: const TextStyle(color: Colors.white, fontSize: 16, fontWeight: FontWeight.bold)),
            Text(sub, style: TextStyle(color: Colors.white.withOpacity(0.3), fontSize: 12)),
          ],
        ),
      ),
    );
  }

  Widget _buildAccountSection(BuildContext context, UserModel? user) {
    return Container(
      decoration: BoxDecoration(
        color: Colors.white.withOpacity(0.03),
        borderRadius: BorderRadius.circular(28),
        border: Border.all(color: Colors.white.withOpacity(0.05)),
      ),
      child: Column(
        children: [
          _buildOptionTile(
            Icons.person_outline_rounded, 
            'Personal Information', 
            const Color(0xFF74B9FF), 
            () => Navigator.push(context, MaterialPageRoute(builder: (_) => const WadexAccountScreen(initialTab: 1))),
          ),
          _buildDivider(),
          _buildOptionTile(
            Icons.security_rounded, 
            'Security', 
            const Color(0xFFF9CA24), 
            () => Navigator.push(context, MaterialPageRoute(builder: (_) => const WadexAccountScreen(initialTab: 2))),
          ),
          _buildDivider(),
          _buildOptionTile(
            Icons.lock_outline_rounded, 
            'Privacy & Data', 
            const Color(0xFFA29BFE), 
            () => Navigator.push(context, MaterialPageRoute(builder: (_) => const WadexAccountScreen(initialTab: 3))),
          ),
        ],
      ),
    );
  }

  Widget _buildPremiumSettings(BuildContext context, WidgetRef ref) {
    return Container(
      decoration: BoxDecoration(
        color: Colors.white.withOpacity(0.03),
        borderRadius: BorderRadius.circular(28),
        border: Border.all(color: Colors.white.withOpacity(0.05)),
      ),
      child: Column(
        children: [
          _buildOptionTile(Icons.settings_suggest_rounded, 'App Preferences', const Color(0xFF00D4AA), () => _showSettingsModal(context, ref)),
          _buildDivider(),
          _buildOptionTile(Icons.dark_mode_outlined, 'Appearance', const Color(0xFF6C63FF), () => showAppearanceModal(context, ref)),
        ],
      ),
    );
  }

  Widget _buildSecurityCard(BuildContext context) {
    return Container(
      decoration: BoxDecoration(
        color: Colors.white.withOpacity(0.03),
        borderRadius: BorderRadius.circular(28),
        border: Border.all(color: Colors.white.withOpacity(0.05)),
      ),
      child: Column(
        children: [
          _buildOptionTile(Icons.description_outlined, 'Legal & Privacy', const Color(0xFFE17055), () => _showLegalModal(context)),
          _buildDivider(),
          _buildOptionTile(Icons.support_agent_rounded, 'Elite Support', const Color(0xFFA29BFE), () => _showSupportModal(context)),
        ],
      ),
    );
  }

  Widget _buildDivider() => Divider(height: 1, color: Colors.white.withOpacity(0.05), indent: 70);

  Widget _buildOptionTile(IconData icon, String title, Color color, VoidCallback onTap) {
    return ListTile(
      onTap: onTap,
      leading: Container(
        padding: const EdgeInsets.all(10),
        decoration: BoxDecoration(color: color.withOpacity(0.1), borderRadius: BorderRadius.circular(14)),
        child: Icon(icon, color: color, size: 22),
      ),
      title: Text(title, style: const TextStyle(color: Colors.white, fontSize: 16, fontWeight: FontWeight.w500)),
      trailing: const Icon(Icons.arrow_forward_ios_rounded, color: Colors.white24, size: 16),
      contentPadding: const EdgeInsets.symmetric(horizontal: 16, vertical: 6),
    );
  }

  Widget _buildPremiumLogout(WidgetRef ref, BuildContext context) {
    return Container(
      width: double.infinity,
      height: 65,
      decoration: BoxDecoration(
        gradient: LinearGradient(
          colors: [const Color(0xFFFF6B6B).withOpacity(0.1), const Color(0xFFFF6B6B).withOpacity(0.05)],
        ),
        borderRadius: BorderRadius.circular(24),
        border: Border.all(color: const Color(0xFFFF6B6B).withOpacity(0.15)),
      ),
      child: Material(
        color: Colors.transparent,
        child: InkWell(
          onTap: () => _showLogoutDialog(context, ref),
          borderRadius: BorderRadius.circular(24),
          child: const Center(
            child: Text(
              'TERMINATE SESSION',
              style: TextStyle(color: Color(0xFFFF6B6B), fontWeight: FontWeight.w900, fontSize: 14, letterSpacing: 2),
            ),
          ),
        ),
      ),
    );
  }

  void _showLogoutDialog(BuildContext context, WidgetRef ref) {
    showGeneralDialog(
      context: context,
      barrierDismissible: true,
      barrierLabel: 'Logout',
      pageBuilder: (ctx, a1, a2) => Container(),
      transitionBuilder: (ctx, a1, a2, child) {
        return Transform.scale(
          scale: a1.value,
          child: Opacity(
            opacity: a1.value,
            child: AlertDialog(
              backgroundColor: const Color(0xFF1A1A2E),
              shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(30)),
              title: const Text('Confirm Logout', style: TextStyle(color: Colors.white, fontWeight: FontWeight.bold)),
              content: const Text('Are you sure you want to end your session?', style: TextStyle(color: Colors.white60)),
              actions: [
                TextButton(onPressed: () => Navigator.pop(ctx), child: const Text('Cancel', style: TextStyle(color: Colors.white38))),
                ElevatedButton(
                  style: ElevatedButton.styleFrom(
                    backgroundColor: const Color(0xFFFF6B6B),
                    shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(14)),
                  ),
                  onPressed: () {
                    Navigator.pop(ctx);
                    ref.read(authProvider.notifier).logout();
                  },
                  child: const Text('Logout', style: TextStyle(color: Colors.white, fontWeight: FontWeight.bold)),
                ),
              ],
            ),
          ),
        );
      },
    );
  }

  void _showSettingsModal(BuildContext context, WidgetRef ref) {
    showModalBottomSheet(
      context: context,
      isScrollControlled: true,
      backgroundColor: Colors.transparent,
      builder: (ctx) => Container(
        height: MediaQuery.of(context).size.height * 0.7,
        decoration: const BoxDecoration(
          color: Color(0xFF1A1A2E),
          borderRadius: BorderRadius.vertical(top: Radius.circular(40)),
        ),
        padding: const EdgeInsets.all(32),
        child: Column(
          crossAxisAlignment: CrossAxisAlignment.start,
          children: [
            Center(child: Container(width: 50, height: 5, decoration: BoxDecoration(color: Colors.white12, borderRadius: BorderRadius.circular(10)))),
            const SizedBox(height: 32),
            const Text('App Preferences', style: TextStyle(fontSize: 28, fontWeight: FontWeight.w900, color: Colors.white)),
            const SizedBox(height: 40),
            _buildPremiumSwitch('PUSH NOTIFICATIONS', Icons.notifications_active_rounded, const Color(0xFF74B9FF), true, (v) {}),
            const SizedBox(height: 24),
            _buildPremiumSwitch('BIOMETRIC LOGIN', Icons.fingerprint_rounded, const Color(0xFF00D4AA), true, (v) {}),
            const SizedBox(height: 24),
            _buildPremiumSwitch('ALWAYS-ON LOCATION', Icons.gps_fixed_rounded, const Color(0xFFFF6B6B), false, (v) {}),
            const Spacer(),
            ElevatedButton(
              onPressed: () => Navigator.pop(ctx),
              style: ElevatedButton.styleFrom(
                backgroundColor: const Color(0xFF6C63FF),
                minimumSize: const Size(double.infinity, 60),
                shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(20)),
              ),
              child: const Text('SAVE PREFERENCES', style: TextStyle(fontWeight: FontWeight.w900, fontSize: 14, letterSpacing: 1)),
            ),
          ],
        ),
      ),
    );
  }

  Widget _buildPremiumSwitch(String label, IconData icon, Color color, bool value, Function(bool) onChanged) {
    return Row(
      children: [
        Container(
          padding: const EdgeInsets.all(12),
          decoration: BoxDecoration(color: color.withOpacity(0.1), borderRadius: BorderRadius.circular(16)),
          child: Icon(icon, color: color, size: 24),
        ),
        const SizedBox(width: 20),
        Expanded(
          child: Text(label, style: const TextStyle(color: Colors.white70, fontSize: 13, fontWeight: FontWeight.w900, letterSpacing: 0.5)),
        ),
        Switch.adaptive(
          value: value, 
          activeColor: color,
          onChanged: onChanged,
        ),
      ],
    );
  }

  void _showSupportModal(BuildContext context) {
    showModalBottomSheet(
      context: context,
      backgroundColor: Colors.transparent,
      builder: (ctx) => Container(
        padding: const EdgeInsets.all(32),
        decoration: const BoxDecoration(
          color: Color(0xFF1A1A2E),
          borderRadius: BorderRadius.vertical(top: Radius.circular(40)),
        ),
        child: Column(
          mainAxisSize: MainAxisSize.min,
          children: [
            const Text('ELITE SUPPORT', style: TextStyle(fontSize: 24, fontWeight: FontWeight.w900, color: Colors.white, letterSpacing: 1)),
            const SizedBox(height: 12),
            const Text('Priority access for Diamond members', style: TextStyle(color: Colors.white38)),
            const SizedBox(height: 40),
            _buildPremiumSupportAction(Icons.headset_mic_rounded, 'Direct Line', '24/7 Priority Support', const Color(0xFF00D4AA), null),
            const SizedBox(height: 20),
            _buildPremiumSupportAction(
              Icons.chat_bubble_rounded, 
              'Live Chat', 
              'Average response: 30s', 
              const Color(0xFF6C63FF),
              () {
                Navigator.pop(ctx); // Close modal
                Navigator.push(context, MaterialPageRoute(builder: (_) => const SupportChatScreen()));
              }
            ),
          ],
        ),
      ),
    );
  }

  Widget _buildPremiumSupportAction(IconData icon, String title, String sub, Color color, VoidCallback? onTap) {
    return InkWell(
      onTap: onTap ?? () => _showComingSoon(context, title),
      borderRadius: BorderRadius.circular(24),
      child: Container(
        padding: const EdgeInsets.all(20),
      decoration: BoxDecoration(
        color: Colors.white.withOpacity(0.05),
        borderRadius: BorderRadius.circular(24),
        border: Border.all(color: color.withOpacity(0.1)),
      ),
      child: Row(
        children: [
          Container(
            padding: const EdgeInsets.all(12),
            decoration: BoxDecoration(color: color.withOpacity(0.1), shape: BoxShape.circle),
            child: Icon(icon, color: color, size: 24),
          ),
          const SizedBox(width: 20),
          Expanded(
            child: Column(
              crossAxisAlignment: CrossAxisAlignment.start,
              children: [
                Text(title, style: const TextStyle(color: Colors.white, fontWeight: FontWeight.bold, fontSize: 16)),
                Text(sub, style: TextStyle(color: Colors.white.withOpacity(0.4), fontSize: 12)),
              ],
            ),
          ),
          const Icon(Icons.chevron_right, color: Colors.white24),
        ],
      ),
    );
  }

  void _showLegalModal(BuildContext context) {
    showModalBottomSheet(
      context: context,
      isScrollControlled: true,
      backgroundColor: Colors.transparent,
      builder: (ctx) => Container(
        height: MediaQuery.of(context).size.height * 0.6,
        padding: const EdgeInsets.all(32),
        decoration: const BoxDecoration(
          color: Color(0xFF1A1A2E),
          borderRadius: BorderRadius.vertical(top: Radius.circular(40)),
        ),
        child: Column(
          children: [
            const Text('LEGAL & PRIVACY', style: TextStyle(fontSize: 22, fontWeight: FontWeight.w900, color: Colors.white, letterSpacing: 1)),
            const SizedBox(height: 40),
            _buildOptionTile(Icons.description_rounded, 'Terms of Service', const Color(0xFF6C63FF), () {}),
            _buildOptionTile(Icons.privacy_tip_rounded, 'Privacy Policy', const Color(0xFF00D4AA), () {}),
            _buildOptionTile(Icons.health_and_safety_rounded, 'Safety Guidelines', const Color(0xFFFF6B6B), () {}),
            const Spacer(),
            const Text('v1.2.4 Build 8892', style: TextStyle(color: Colors.white12, fontSize: 10, letterSpacing: 1)),
          ],
        ),
      ),
    );
  }

  void _showComingSoon(BuildContext context, String feature) {
    ScaffoldMessenger.of(context).showSnackBar(
      SnackBar(
        content: Text('$feature — Exclusive Diamond Access Coming Soon'),
        backgroundColor: const Color(0xFF6C63FF),
        behavior: SnackBarBehavior.floating,
        shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(16)),
      ),
    );
  }
}
