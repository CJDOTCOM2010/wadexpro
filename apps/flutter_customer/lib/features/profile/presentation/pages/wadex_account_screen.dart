import 'dart:io';
import 'package:flutter/material.dart';
import 'package:flutter/foundation.dart' show kIsWeb;
import 'package:flutter_riverpod/flutter_riverpod.dart';
import 'package:image_picker/image_picker.dart';
import 'package:image_cropper/image_cropper.dart';
import 'package:url_launcher/url_launcher.dart';
import 'package:flutter_animate/flutter_animate.dart';
import 'package:wadexpro_customer/core/theme/app_colors.dart';
import 'package:wadexpro_customer/features/auth/presentation/providers/auth_provider.dart';
import 'package:wadexpro_customer/core/models/user_model.dart';
import '../../../../core/widgets/wadex_snackbar.dart';

class WadexAccountScreen extends ConsumerStatefulWidget {
  final int initialTab;
  const WadexAccountScreen({super.key, this.initialTab = 0});

  @override
  ConsumerState<WadexAccountScreen> createState() => _WadexAccountScreenState();
}

class _WadexAccountScreenState extends ConsumerState<WadexAccountScreen> with SingleTickerProviderStateMixin {
  late TabController _tabCtrl;
  final ImagePicker _picker = ImagePicker();

  @override
  void initState() {
    super.initState();
    _tabCtrl = TabController(length: 4, vsync: this, initialIndex: widget.initialTab);
    _tabCtrl.addListener(() => setState(() {}));
    
    // Fetch latest profile data when opening account screen
    WidgetsBinding.instance.addPostFrameCallback((_) {
      ref.read(authProvider.notifier).fetchProfile();
    });
  }

  @override
  void dispose() {
    _tabCtrl.dispose();
    super.dispose();
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
    final user = authState.user;

    return Scaffold(
      backgroundColor: AppColors.obsidianDark,
      body: NestedScrollView(
        headerSliverBuilder: (context, innerBoxIsScrolled) => [
          SliverAppBar(
            expandedHeight: 180,
            pinned: true,
            backgroundColor: AppColors.obsidianDark,
            elevation: 0,
            leading: IconButton(
              icon: const Icon(Icons.arrow_back_ios_new_rounded, color: Colors.white, size: 20),
              onPressed: () => Navigator.pop(context),
            ),
            flexibleSpace: FlexibleSpaceBar(
              background: Stack(
                alignment: Alignment.center,
                children: [
                  Container(
                    decoration: BoxDecoration(
                      gradient: LinearGradient(
                        colors: [const Color(0xFF6C63FF).withOpacity(0.2), AppColors.obsidianDark],
                        begin: Alignment.topCenter,
                        end: Alignment.bottomCenter,
                      ),
                    ),
                  ),
                  Positioned(
                    top: 60,
                    child: Column(
                      children: [
                        Text(
                          'WADEX Account',
                          style: TextStyle(
                            color: Colors.white.withOpacity(0.4),
                            fontSize: 12,
                            fontWeight: FontWeight.w900,
                            letterSpacing: 2,
                          ),
                        ),
                        const SizedBox(height: 8),
                        Text(
                          user?.name ?? 'Secure User',
                          style: const TextStyle(
                            color: Colors.white,
                            fontSize: 24,
                            fontWeight: FontWeight.w900,
                            letterSpacing: -0.5,
                          ),
                        ),
                      ],
                    ),
                  ),
                ],
              ),
            ),
            bottom: PreferredSize(
              preferredSize: const Size.fromHeight(60),
              child: Container(
                padding: const EdgeInsets.symmetric(horizontal: 20),
                alignment: Alignment.centerLeft,
                child: TabBar(
                  controller: _tabCtrl,
                  isScrollable: true,
                  tabAlignment: TabAlignment.start,
                  dividerColor: Colors.transparent,
                  indicatorColor: const Color(0xFF6C63FF),
                  indicatorSize: TabBarIndicatorSize.label,
                  labelColor: Colors.white,
                  unselectedLabelColor: Colors.white30,
                  labelStyle: const TextStyle(fontWeight: FontWeight.w900, fontSize: 13, letterSpacing: 0.5),
                  tabs: const [
                    Tab(text: 'OVERVIEW'),
                    Tab(text: 'PERSONAL INFO'),
                    Tab(text: 'SECURITY'),
                    Tab(text: 'PRIVACY & DATA'),
                  ],
                ),
              ),
            ),
          ),
        ],
        body: TabBarView(
          controller: _tabCtrl,
          children: [
            _HomeTab(user: user, tabCtrl: _tabCtrl),
            _PersonalInfoTab(user: user, onUpdateAvatar: _updateProfileImage),
            _SecurityTab(user: user),
            _PrivacyTab(),
          ],
        ),
      ),
    );
  }
}

class _HomeTab extends StatelessWidget {
  final UserModel? user;
  final TabController tabCtrl;
  const _HomeTab({required this.user, required this.tabCtrl});

  @override
  Widget build(BuildContext context) {
    return SingleChildScrollView(
      padding: const EdgeInsets.all(24),
      child: Column(
        crossAxisAlignment: CrossAxisAlignment.start,
        children: [
          _buildEliteCard(
            title: 'Your profile at a glance',
            subtitle: 'Manage your info, privacy and security to make WADEXPRO work better for you.',
            icon: Icons.shield_moon_rounded,
            color: const Color(0xFF6C63FF),
            actionLabel: 'Perform Security Check',
            onAction: () => tabCtrl.animateTo(2),
          ),
          const SizedBox(height: 32),
          const Text('QUICK ACCESS', style: TextStyle(color: Colors.white24, fontWeight: FontWeight.w900, fontSize: 11, letterSpacing: 1.5)),
          const SizedBox(height: 16),
          Row(
            children: [
              _buildQuickTile(Icons.person_pin_rounded, 'Personal', const Color(0xFF74B9FF), () => tabCtrl.animateTo(1)),
              const SizedBox(width: 12),
              _buildQuickTile(Icons.verified_user_rounded, 'Security', const Color(0xFFF9CA24), () => tabCtrl.animateTo(2)),
              const SizedBox(width: 12),
              _buildQuickTile(Icons.lock_person_rounded, 'Privacy', const Color(0xFFA29BFE), () => tabCtrl.animateTo(3)),
            ],
          ),
          const SizedBox(height: 32),
          _buildInfoSuggestion(
            title: 'Complete your profile',
            body: 'Adding a photo and your birthday helps us personalize your experience.',
            onTap: () => tabCtrl.animateTo(1),
          ),
        ],
      ).animate().fadeIn(duration: 400.ms).slideY(begin: 0.05),
    );
  }

  Widget _buildEliteCard({required String title, required String subtitle, required IconData icon, required Color color, required String actionLabel, required VoidCallback onAction}) {
    return Container(
      padding: const EdgeInsets.all(24),
      decoration: BoxDecoration(
        color: Colors.white.withOpacity(0.03),
        borderRadius: BorderRadius.circular(30),
        border: Border.all(color: Colors.white.withOpacity(0.05)),
      ),
      child: Column(
        crossAxisAlignment: CrossAxisAlignment.start,
        children: [
          Row(
            children: [
              Container(
                padding: const EdgeInsets.all(12),
                decoration: BoxDecoration(color: color.withOpacity(0.1), shape: BoxShape.circle),
                child: Icon(icon, color: color, size: 28),
              ),
              const SizedBox(width: 16),
              Expanded(
                child: Column(
                  crossAxisAlignment: CrossAxisAlignment.start,
                  children: [
                    Text(title, style: const TextStyle(color: Colors.white, fontSize: 18, fontWeight: FontWeight.bold)),
                    const SizedBox(height: 4),
                    Text(subtitle, style: TextStyle(color: Colors.white.withOpacity(0.4), fontSize: 13)),
                  ],
                ),
              ),
            ],
          ),
          const SizedBox(height: 24),
          ElevatedButton(
            onPressed: onAction,
            style: ElevatedButton.styleFrom(
              backgroundColor: color,
              minimumSize: const Size(double.infinity, 50),
              shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(16)),
            ),
            child: Text(actionLabel, style: const TextStyle(fontWeight: FontWeight.bold, color: Colors.white)),
          ),
        ],
      ),
    );
  }

  Widget _buildQuickTile(IconData icon, String label, Color color, VoidCallback onTap) {
    return Expanded(
      child: InkWell(
        onTap: onTap,
        borderRadius: BorderRadius.circular(20),
        child: Container(
          padding: const EdgeInsets.symmetric(vertical: 20),
          decoration: BoxDecoration(
            color: Colors.white.withOpacity(0.03),
            borderRadius: BorderRadius.circular(20),
            border: Border.all(color: Colors.white.withOpacity(0.05)),
          ),
          child: Column(
            children: [
              Icon(icon, color: color, size: 26),
              const SizedBox(height: 10),
              Text(label, style: const TextStyle(color: Colors.white70, fontSize: 12, fontWeight: FontWeight.bold)),
            ],
          ),
        ),
      ),
    );
  }

  Widget _buildInfoSuggestion({required String title, required String body, required VoidCallback onTap}) {
    return Container(
      width: double.infinity,
      padding: const EdgeInsets.all(20),
      decoration: BoxDecoration(
        gradient: LinearGradient(colors: [Colors.white.withOpacity(0.05), Colors.white.withOpacity(0.02)]),
        borderRadius: BorderRadius.circular(24),
        border: Border.all(color: Colors.white.withOpacity(0.05)),
      ),
      child: Column(
        crossAxisAlignment: CrossAxisAlignment.start,
        children: [
          Text(title, style: const TextStyle(color: Colors.white, fontSize: 16, fontWeight: FontWeight.bold)),
          const SizedBox(height: 8),
          Text(body, style: TextStyle(color: Colors.white.withOpacity(0.3), fontSize: 14)),
          const SizedBox(height: 16),
          TextButton(onPressed: onTap, child: const Text('Update Profile', style: TextStyle(color: Color(0xFF00D4AA), fontWeight: FontWeight.bold))),
        ],
      ),
    );
  }
}

class _PersonalInfoTab extends ConsumerWidget {
  final UserModel? user;
  final VoidCallback onUpdateAvatar;
  const _PersonalInfoTab({required this.user, required this.onUpdateAvatar});

  @override
  Widget build(BuildContext context, WidgetRef ref) {
    return SingleChildScrollView(
      padding: const EdgeInsets.all(24),
      child: Column(
        crossAxisAlignment: CrossAxisAlignment.start,
        children: [
          const Text('BASIC INFORMATION', style: TextStyle(color: Colors.white24, fontWeight: FontWeight.w900, fontSize: 11, letterSpacing: 1.5)),
          const SizedBox(height: 16),
          _buildPremiumInfoCard(
            context,
            'Profile Photo',
            'A photo helps personalize your account.',
            leading: CircleAvatar(
              radius: 20,
              backgroundImage: user?.avatarUrl != null 
                ? (user!.avatarUrl!.startsWith('http') 
                    ? NetworkImage(user!.avatarUrl!) 
                    : FileImage(File(user!.avatarUrl!)) as ImageProvider)
                : null,
              child: user?.avatarUrl == null ? const Icon(Icons.person) : null,
            ),
            onTap: onUpdateAvatar,
          ),
          _buildPremiumInfoCard(context, 'Display Name', user?.name ?? 'Set your name', onTap: () => _showEditDialog(context, ref, 'Name', user?.name ?? '')),
          _buildPremiumInfoCard(context, 'Gender', user?.driverProfile?['gender'] ?? 'Prefer not to say', onTap: () => _showGenderPicker(context, ref)),
          _buildPremiumInfoCard(context, 'Birthday', 'August 12, 1994', onTap: () {}),
          const SizedBox(height: 32),
          const Text('CONTACT INFORMATION', style: TextStyle(color: Colors.white24, fontWeight: FontWeight.w900, fontSize: 11, letterSpacing: 1.5)),
          const SizedBox(height: 16),
          _buildPremiumInfoCard(context, 'Email Address', user?.email ?? 'No email set', verified: true, onTap: () => _showEditDialog(context, ref, 'Email', user?.email ?? '')),
          _buildPremiumInfoCard(context, 'Phone Number', user?.phone ?? 'No phone set', verified: true, onTap: () {}),
        ],
      ).animate().fadeIn(duration: 400.ms),
    );
  }

  Widget _buildPremiumInfoCard(BuildContext context, String label, String value, {Widget? leading, bool verified = false, VoidCallback? onTap}) {
    return Container(
      margin: const EdgeInsets.only(bottom: 12),
      decoration: BoxDecoration(
        color: Colors.white.withOpacity(0.03),
        borderRadius: BorderRadius.circular(20),
        border: Border.all(color: Colors.white.withOpacity(0.05)),
      ),
      child: ListTile(
        onTap: onTap,
        contentPadding: const EdgeInsets.symmetric(horizontal: 20, vertical: 8),
        leading: leading,
        title: Text(label, style: const TextStyle(color: Colors.white54, fontSize: 12, fontWeight: FontWeight.bold)),
        subtitle: Row(
          children: [
            Flexible(child: Text(value, style: const TextStyle(color: Colors.white, fontSize: 15, fontWeight: FontWeight.bold), overflow: TextOverflow.ellipsis)),
            if (verified) ...[const SizedBox(width: 8), const Icon(Icons.verified, color: Color(0xFF00D4AA), size: 16)],
          ],
        ),
        trailing: const Icon(Icons.arrow_forward_ios_rounded, color: Colors.white12, size: 14),
      ),
    );
  }

  void _showEditDialog(BuildContext ctx, WidgetRef ref, String field, String current) {
    final ctrl = TextEditingController(text: current);
    showGeneralDialog(
      context: ctx,
      barrierDismissible: true,
      barrierLabel: 'Edit',
      pageBuilder: (c, a1, a2) => AlertDialog(
        backgroundColor: const Color(0xFF1A1A2E),
        shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(24)),
        title: Text('Update $field', style: const TextStyle(color: Colors.white, fontWeight: FontWeight.bold)),
        content: TextField(
          controller: ctrl,
          style: const TextStyle(color: Colors.white),
          decoration: InputDecoration(
            hintText: 'New $field',
            hintStyle: const TextStyle(color: Colors.white24),
            enabledBorder: const UnderlineInputBorder(borderSide: BorderSide(color: Colors.white10)),
          ),
          autofocus: true,
        ),
        actions: [
          TextButton(onPressed: () => Navigator.pop(c), child: const Text('CANCEL', style: TextStyle(color: Colors.white38))),
          ElevatedButton(
            onPressed: () async {
              final val = ctrl.text.trim();
              Navigator.pop(c);
              bool success = false;
              if (field == 'Name') success = await ref.read(authProvider.notifier).updateProfile(name: val);
              if (field == 'Email') success = await ref.read(authProvider.notifier).updateProfile(email: val);
              
              if (ctx.mounted) {
                if (success) {
                  WadexSnackBar.showSuccess(ctx, '$field updated');
                } else {
                  WadexSnackBar.showError(ctx, 'Update failed');
                }
              }
            },
            child: const Text('SAVE'),
          ),
        ],
      ),
    );
  }

  void _showGenderPicker(BuildContext ctx, WidgetRef ref) {
    showModalBottomSheet(
      context: ctx,
      backgroundColor: const Color(0xFF1A1A2E),
      shape: const RoundedRectangleBorder(borderRadius: BorderRadius.vertical(top: Radius.circular(30))),
      builder: (c) => Padding(
        padding: const EdgeInsets.all(32),
        child: Column(
          mainAxisSize: MainAxisSize.min,
          children: [
            const Text('Select Gender', style: TextStyle(color: Colors.white, fontSize: 20, fontWeight: FontWeight.bold)),
            const SizedBox(height: 24),
            for (final g in ['Male', 'Female', 'Non-binary'])
              ListTile(
                title: Text(g, style: const TextStyle(color: Colors.white)),
                onTap: () async {
                  Navigator.pop(c);
                  final success = await ref.read(authProvider.notifier).updateProfile(gender: g);
                  if (ctx.mounted) {
                    if (success) {
                      WadexSnackBar.showSuccess(ctx, 'Gender set to $g');
                    } else {
                      WadexSnackBar.showError(ctx, 'Update failed');
                    }
                  }
                },
              ),
          ],
        ),
      ),
    );
  }
}

class _SecurityTab extends StatelessWidget {
  final UserModel? user;
  const _SecurityTab({required this.user});

  @override
  Widget build(BuildContext context) {
    return SingleChildScrollView(
      padding: const EdgeInsets.all(24),
      child: Column(
        crossAxisAlignment: CrossAxisAlignment.start,
        children: [
          const Text('CREDENTIALS', style: TextStyle(color: Colors.white24, fontWeight: FontWeight.w900, fontSize: 11, letterSpacing: 1.5)),
          const SizedBox(height: 16),
          _buildSecurityCard('Password', 'Last changed 3 months ago', Icons.password_rounded),
          _buildSecurityCard('Passkeys', 'Biometric login is active', Icons.fingerprint_rounded),
          _buildSecurityCard('2-Step Verification', 'Protect your account with an extra layer', Icons.verified_user_rounded),
          const SizedBox(height: 32),
          const Text('CONNECTED SOCIALS', style: TextStyle(color: Colors.white24, fontWeight: FontWeight.w900, fontSize: 11, letterSpacing: 1.5)),
          const SizedBox(height: 16),
          _buildSocialRow('Google', Icons.g_mobiledata, true),
          _buildSocialRow('Apple ID', Icons.apple, false),
          _buildSocialRow('Facebook', Icons.facebook, false),
        ],
      ).animate().fadeIn(duration: 400.ms),
    );
  }

  Widget _buildSecurityCard(String title, String sub, IconData icon) {
    return Container(
      margin: const EdgeInsets.only(bottom: 12),
      padding: const EdgeInsets.all(16),
      decoration: BoxDecoration(
        color: Colors.white.withOpacity(0.03),
        borderRadius: BorderRadius.circular(20),
        border: Border.all(color: Colors.white.withOpacity(0.05)),
      ),
      child: Row(
        children: [
          Container(
            padding: const EdgeInsets.all(12),
            decoration: BoxDecoration(color: Colors.white.withOpacity(0.05), borderRadius: BorderRadius.circular(16)),
            child: Icon(icon, color: Colors.white70, size: 24),
          ),
          const SizedBox(width: 16),
          Expanded(
            child: Column(
              crossAxisAlignment: CrossAxisAlignment.start,
              children: [
                Text(title, style: const TextStyle(color: Colors.white, fontWeight: FontWeight.bold, fontSize: 16)),
                const SizedBox(height: 4),
                Text(sub, style: TextStyle(color: Colors.white.withOpacity(0.3), fontSize: 12)),
              ],
            ),
          ),
          const Icon(Icons.chevron_right, color: Colors.white12),
        ],
      ),
    );
  }

  Widget _buildSocialRow(String name, IconData icon, bool connected) {
    return Container(
      margin: const EdgeInsets.only(bottom: 12),
      padding: const EdgeInsets.symmetric(horizontal: 20, vertical: 12),
      decoration: BoxDecoration(
        color: Colors.white.withOpacity(0.03),
        borderRadius: BorderRadius.circular(20),
        border: Border.all(color: Colors.white.withOpacity(0.05)),
      ),
      child: Row(
        children: [
          Icon(icon, color: Colors.white, size: 28),
          const SizedBox(width: 16),
          Text(name, style: const TextStyle(color: Colors.white, fontWeight: FontWeight.bold)),
          const Spacer(),
          Text(
            connected ? 'CONNECTED' : 'DISCONNECTED',
            style: TextStyle(
              color: connected ? const Color(0xFF00D4AA) : Colors.white24,
              fontSize: 10,
              fontWeight: FontWeight.w900,
              letterSpacing: 1,
            ),
          ),
        ],
      ),
    );
  }
}

class _PrivacyTab extends ConsumerWidget {
  @override
  Widget build(BuildContext context, WidgetRef ref) {
    return SingleChildScrollView(
      padding: const EdgeInsets.all(24),
      child: Column(
        crossAxisAlignment: CrossAxisAlignment.start,
        children: [
          const Text('PRIVACY PREFERENCES', style: TextStyle(color: Colors.white24, fontWeight: FontWeight.w900, fontSize: 11, letterSpacing: 1.5)),
          const SizedBox(height: 16),
          _buildPrivacyTile('Privacy Center', 'Learn how we protect your data.', Icons.privacy_tip_rounded),
          _buildPrivacyTile('Marketing Communication', 'Emails, push notifications and SMS.', Icons.email_rounded),
          _buildPrivacyTile('Personalized Content', 'Tailored experiences based on your usage.', Icons.interests_rounded),
          const SizedBox(height: 48),
          Text('DANGER ZONE', style: TextStyle(color: Color(0xFFFF6B6B).withOpacity(0.5), fontWeight: FontWeight.w900, fontSize: 11, letterSpacing: 1.5)),
          const SizedBox(height: 16),
          Container(
            padding: const EdgeInsets.all(20),
            decoration: BoxDecoration(
              color: const Color(0xFFFF6B6B).withOpacity(0.03),
              borderRadius: BorderRadius.circular(24),
              border: Border.all(color: const Color(0xFFFF6B6B).withOpacity(0.1)),
            ),
            child: Column(
              crossAxisAlignment: CrossAxisAlignment.start,
              children: [
                const Text('Permanent Deletion', style: TextStyle(color: Colors.white, fontWeight: FontWeight.bold, fontSize: 16)),
                const SizedBox(height: 8),
                Text(
                  'Deleting your account will remove all your trip history, wallet balance, and membership status. This action is irreversible.',
                  style: TextStyle(color: Colors.white.withOpacity(0.3), fontSize: 13, height: 1.5),
                ),
                const SizedBox(height: 20),
                ElevatedButton(
                  onPressed: () => _showDeleteDialog(context, ref),
                  style: ElevatedButton.styleFrom(
                    backgroundColor: const Color(0xFFFF6B6B).withOpacity(0.1),
                    foregroundColor: const Color(0xFFFF6B6B),
                    minimumSize: const Size(double.infinity, 50),
                    elevation: 0,
                    shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(14)),
                  ),
                  child: const Text('Delete My Account', style: TextStyle(fontWeight: FontWeight.w900)),
                ),
              ],
            ),
          ),
        ],
      ).animate().fadeIn(duration: 400.ms),
    );
  }

  Widget _buildPrivacyTile(String title, String sub, IconData icon) {
    return Container(
      margin: const EdgeInsets.only(bottom: 12),
      decoration: BoxDecoration(
        color: Colors.white.withOpacity(0.03),
        borderRadius: BorderRadius.circular(20),
        border: Border.all(color: Colors.white.withOpacity(0.05)),
      ),
      child: ListTile(
        leading: Icon(icon, color: Colors.white54),
        title: Text(title, style: const TextStyle(color: Colors.white, fontWeight: FontWeight.bold, fontSize: 15)),
        subtitle: Text(sub, style: TextStyle(color: Colors.white.withOpacity(0.3), fontSize: 12)),
        trailing: const Icon(Icons.chevron_right, color: Colors.white12, size: 16),
        onTap: () {},
      ),
    );
  }

  void _showDeleteDialog(BuildContext ctx, WidgetRef ref) {
    showGeneralDialog(
      context: ctx,
      barrierDismissible: true,
      barrierLabel: 'Delete',
      pageBuilder: (c, a1, a2) => AlertDialog(
        backgroundColor: const Color(0xFF1A1A2E),
        shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(24)),
        title: const Text('Final Confirmation', style: TextStyle(color: Colors.white, fontWeight: FontWeight.bold)),
        content: const Text('Are you 100% sure? All your data will be purged from our servers.', style: TextStyle(color: Colors.white60)),
        actions: [
          TextButton(onPressed: () => Navigator.pop(c), child: const Text('CANCEL', style: TextStyle(color: Colors.white38))),
          ElevatedButton(
            style: ElevatedButton.styleFrom(backgroundColor: const Color(0xFFFF6B6B)),
            onPressed: () {
              Navigator.pop(c);
              Navigator.pop(ctx);
              ref.read(authProvider.notifier).deleteAccount();
            },
            child: const Text('PURGE DATA', style: TextStyle(color: Colors.white, fontWeight: FontWeight.bold)),
          ),
        ],
      ),
    );
  }
}
