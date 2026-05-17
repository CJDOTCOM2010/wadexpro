import 'package:flutter/material.dart';
import 'package:flutter_riverpod/flutter_riverpod.dart';
import 'package:image_picker/image_picker.dart';
import '../../../../core/theme/app_colors.dart';
import '../providers/profile_provider.dart';
import 'package:wadexpro_driver/features/wallet/presentation/pages/wallet_screen.dart';
import '../../../auth/presentation/providers/auth_provider.dart';
import 'kyc_upload_screen.dart';

class ProfileScreen extends ConsumerStatefulWidget {
  const ProfileScreen({super.key});

  @override
  ConsumerState<ProfileScreen> createState() => _ProfileScreenState();
}

class _ProfileScreenState extends ConsumerState<ProfileScreen> {

  @override
  void initState() {
    super.initState();
    WidgetsBinding.instance.addPostFrameCallback((_) {
      ref.read(profileProvider.notifier).fetchProfile();
      ref.read(authProvider.notifier).fetchProfile(); // ensure base user is updated
    });
  }

  @override
  Widget build(BuildContext context) {
    final profileState = ref.watch(profileProvider);

    return Scaffold(
      backgroundColor: AppColors.background,
      appBar: AppBar(
        title: const Text('My Profile', style: TextStyle(fontWeight: FontWeight.bold)),
        backgroundColor: Colors.transparent,
        elevation: 0,
        actions: [
          IconButton(
            onPressed: () => ref.read(profileProvider.notifier).fetchProfile(),
            icon: const Icon(Icons.refresh),
          ),
        ],
      ),
      body: profileState.isLoading
          ? const Center(child: CircularProgressIndicator())
          : SingleChildScrollView(
              padding: const EdgeInsets.all(20),
              child: Column(
                children: [
                  _buildProfileHeader(profileState),
                  const SizedBox(height: 24),
                  _buildWalletSummaryTile(context),
                  const SizedBox(height: 24),
                  _buildVehicleSection(profileState),
                  const SizedBox(height: 24),
                  _buildKYCSection(profileState),
                  const SizedBox(height: 40),
                  ElevatedButton(
                    onPressed: () {
                      // Sign out logic
                    },
                    style: ElevatedButton.styleFrom(
                      backgroundColor: Colors.red.shade50,
                      foregroundColor: Colors.red,
                      minimumSize: const Size(double.infinity, 56),
                      elevation: 0,
                    ),
                    child: const Text('LOG OUT', style: TextStyle(fontWeight: FontWeight.bold)),
                  ),
                ],
              ),
            ),
    );
  }

  Widget _buildProfileHeader(ProfileState state) {
    final user = state.data;
    final status = state.verificationStatus;
    
    Color statusColor;
    IconData statusIcon;
    switch (status) {
      case 'verified':
        statusColor = Colors.green;
        statusIcon = Icons.verified;
        break;
      case 'pending':
        statusColor = Colors.orange;
        statusIcon = Icons.history;
        break;
      case 'rejected':
        statusColor = Colors.red;
        statusIcon = Icons.error_outline;
        break;
      default:
        statusColor = Colors.grey;
        statusIcon = Icons.help_outline;
    }

    return Container(
      padding: const EdgeInsets.all(24),
      decoration: BoxDecoration(
        color: AppColors.primaryNavy,
        borderRadius: BorderRadius.circular(24),
      ),
      child: Row(
        children: [
          CircleAvatar(
            radius: 40,
            backgroundColor: Colors.white10,
            child: const Icon(Icons.person, size: 40, color: Colors.white),
          ),
          const SizedBox(width: 20),
          Expanded(
            child: Column(
              crossAxisAlignment: CrossAxisAlignment.start,
              children: [
                Text(
                  '${user?['first_name'] ?? 'Driver'} ${user?['last_name'] ?? ''}',
                  style: const TextStyle(color: Colors.white, fontSize: 20, fontWeight: FontWeight.bold),
                ),
                const SizedBox(height: 4),
                Container(
                  padding: const EdgeInsets.symmetric(horizontal: 10, vertical: 4),
                  decoration: BoxDecoration(
                    color: statusColor.withOpacity(0.2),
                    borderRadius: BorderRadius.circular(20),
                    border: Border.all(color: statusColor.withOpacity(0.5)),
                  ),
                  child: Row(
                    mainAxisSize: MainAxisSize.min,
                    children: [
                      Icon(statusIcon, color: statusColor, size: 14),
                      const SizedBox(width: 4),
                      Text(
                        status.toUpperCase(),
                        style: TextStyle(color: statusColor, fontSize: 10, fontWeight: FontWeight.bold),
                      ),
                    ],
                  ),
                ),
              ],
            ),
          ),
        ],
      ),
    );
  }

  Widget _buildVehicleSection(ProfileState state) {
    final user = state.data;
    return Container(
      padding: const EdgeInsets.all(24),
      decoration: BoxDecoration(color: Colors.white, borderRadius: BorderRadius.circular(24)),
      child: Column(
        crossAxisAlignment: CrossAxisAlignment.start,
        children: [
          const Row(
            children: [
              Icon(Icons.directions_car, color: AppColors.primaryNavy),
              SizedBox(width: 12),
              Text('VEHICLE INFORMATION', style: TextStyle(fontWeight: FontWeight.bold, letterSpacing: 1)),
            ],
          ),
          const SizedBox(height: 20),
          _buildInfoRow('Model', user?['vehicle_model'] ?? 'Not Set'),
          _buildInfoRow('Plate No', user?['vehicle_plate'] ?? 'Not Set'),
          _buildInfoRow('Color', user?['vehicle_color'] ?? 'Not Set'),
          const SizedBox(height: 12),
          TextButton.icon(
            onPressed: () => _showEditVehicleDialog(user),
            icon: const Icon(Icons.edit, size: 16),
            label: const Text('Update Vehicle Details'),
          ),
        ],
      ),
    );
  }

  Widget _buildWalletSummaryTile(BuildContext context) {
    return InkWell(
      onTap: () => Navigator.push(context, MaterialPageRoute(builder: (_) => WalletScreen())),
      child: Container(
        padding: const EdgeInsets.all(24),
        decoration: BoxDecoration(
          color: Colors.white,
          borderRadius: BorderRadius.circular(24),
        ),
        child: Row(
          children: [
            Container(
              padding: const EdgeInsets.all(12),
              decoration: BoxDecoration(color: AppColors.accent.withOpacity(0.1), shape: BoxShape.circle),
              child: const Icon(Icons.wallet, color: AppColors.accent),
            ),
            const SizedBox(width: 16),
            const Expanded(
              child: Column(
                crossAxisAlignment: CrossAxisAlignment.start,
                children: [
                  Text('Earnings & Wallet', style: TextStyle(fontWeight: FontWeight.bold)),
                  Text('Withdraw funds and manage accounts', style: TextStyle(fontSize: 12, color: AppColors.textMuted)),
                ],
              ),
            ),
            const Icon(Icons.chevron_right, color: AppColors.textMuted),
          ],
        ),
      ),
    );
  }

  Widget _buildInfoRow(String label, String value) {
    return Padding(
      padding: const EdgeInsets.symmetric(vertical: 8),
      child: Row(
        mainAxisAlignment: MainAxisAlignment.spaceBetween,
        children: [
          Text(label, style: const TextStyle(color: AppColors.textMuted)),
          Text(value, style: const TextStyle(fontWeight: FontWeight.bold, color: AppColors.primaryNavy)),
        ],
      ),
    );
  }

  Widget _buildKYCSection(ProfileState state) {
    final status = state.verificationStatus;
    
    return Container(
      padding: const EdgeInsets.all(24),
      decoration: BoxDecoration(color: Colors.white, borderRadius: BorderRadius.circular(24)),
      child: Column(
        crossAxisAlignment: CrossAxisAlignment.start,
        children: [
          const Row(
            children: [
              Icon(Icons.assignment_ind, color: AppColors.primaryNavy),
              SizedBox(width: 12),
              Text('KYC / DOCUMENTS', style: TextStyle(fontWeight: FontWeight.bold, letterSpacing: 1)),
            ],
          ),
          const SizedBox(height: 16),
          if (status == 'verified')
            Container(
              padding: const EdgeInsets.all(16),
              decoration: BoxDecoration(color: Colors.green.withOpacity(0.1), borderRadius: BorderRadius.circular(12)),
              child: const Row(
                children: [
                  Icon(Icons.check_circle, color: Colors.green),
                  SizedBox(width: 12),
                  Expanded(child: Text('Your documents are verified and approved.', style: TextStyle(color: Colors.green, fontWeight: FontWeight.bold))),
                ],
              ),
            )
          else if (status == 'pending_verification' || status == 'pending')
            Container(
              padding: const EdgeInsets.all(16),
              decoration: BoxDecoration(color: Colors.orange.withOpacity(0.1), borderRadius: BorderRadius.circular(12)),
              child: const Row(
                children: [
                  Icon(Icons.access_time_filled, color: Colors.orange),
                  SizedBox(width: 12),
                  Expanded(child: Text('Your documents are currently under review.', style: TextStyle(color: Colors.orange, fontWeight: FontWeight.bold))),
                ],
              ),
            )
          else
            Column(
              children: [
                if (status == 'rejected')
                  Container(
                    margin: const EdgeInsets.only(bottom: 16),
                    padding: const EdgeInsets.all(16),
                    decoration: BoxDecoration(color: Colors.red.withOpacity(0.1), borderRadius: BorderRadius.circular(12)),
                    child: Row(
                      children: [
                        const Icon(Icons.error, color: Colors.red),
                        const SizedBox(width: 12),
                        Expanded(child: Text('Documents rejected. Please re-upload clear copies.', style: TextStyle(color: Colors.red.shade700))),
                      ],
                    ),
                  ),
                ListTile(
                  contentPadding: EdgeInsets.zero,
                  title: const Text('Identity & License', style: TextStyle(fontWeight: FontWeight.bold)),
                  subtitle: const Text('Required for verification'),
                  trailing: ElevatedButton(
                    onPressed: () {
                      Navigator.push(context, MaterialPageRoute(builder: (_) => const KYCUploadScreen()));
                    },
                    style: ElevatedButton.styleFrom(
                      backgroundColor: const Color(0xFF6C63FF),
                      shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(8)),
                    ),
                    child: const Text('UPLOAD', style: TextStyle(color: Colors.white, fontWeight: FontWeight.bold)),
                  ),
                ),
              ],
            ),
        ],
      ),
    );
  }

  void _showEditVehicleDialog(Map<String, dynamic>? user) {
    final modelController = TextEditingController(text: user?['vehicle_model']);
    final plateController = TextEditingController(text: user?['vehicle_plate']);
    final colorController = TextEditingController(text: user?['vehicle_color']);

    showDialog(
      context: context,
      builder: (context) => AlertDialog(
        title: const Text('Vehicle Details'),
        content: Column(
          mainAxisSize: MainAxisSize.min,
          children: [
            TextField(controller: modelController, decoration: const InputDecoration(labelText: 'Model (e.g. Toyota Corolla)')),
            TextField(controller: plateController, decoration: const InputDecoration(labelText: 'Plate Number')),
            TextField(controller: colorController, decoration: const InputDecoration(labelText: 'Color')),
          ],
        ),
        actions: [
          TextButton(onPressed: () => Navigator.pop(context), child: const Text('CANCEL')),
          ElevatedButton(
            onPressed: () {
              ref.read(profileProvider.notifier).updateVehicleInfo(
                model: modelController.text,
                plate: plateController.text,
                color: colorController.text,
              );
              Navigator.pop(context);
            },
            child: const Text('SAVE'),
          ),
        ],
      ),
    );
  }
}
