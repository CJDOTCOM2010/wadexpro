import 'package:flutter/material.dart';
import 'package:flutter_riverpod/flutter_riverpod.dart';
import '../providers/auth_provider.dart';
import '../../../../core/theme/app_colors.dart';

import '../../../../core/config/app_config.dart';

class LoginScreen extends ConsumerStatefulWidget {
  const LoginScreen({super.key});

  @override
  ConsumerState<LoginScreen> createState() => _LoginScreenState();
}

class _LoginScreenState extends ConsumerState<LoginScreen> {
  final _phoneController = TextEditingController();

  @override
  Widget build(BuildContext context) {
    final authState = ref.watch(authProvider);
    final isLoading = authState.status == AuthStatus.loading;

    ref.listen(authProvider, (previous, next) {
      debugPrint('Driver Auth Status Change: ${previous?.status} -> ${next.status}');
      if (next.status == AuthStatus.error) {
        debugPrint('Driver Auth Error: ${next.errorMessage}');
        ScaffoldMessenger.of(context).showSnackBar(
          SnackBar(
            content: Text(next.errorMessage ?? 'An error occurred'),
            backgroundColor: AppColors.error,
            duration: const Duration(seconds: 4),
          ),
        );
      }
    });

    return Scaffold(
      body: SafeArea(
        child: SingleChildScrollView(
          child: Padding(
            padding: const EdgeInsets.symmetric(horizontal: 24.0),
            child: Column(
              crossAxisAlignment: CrossAxisAlignment.start,
              children: [
                const SizedBox(height: 60),
                const Icon(Icons.drive_eta, size: 64, color: AppColors.primary),
                const SizedBox(height: 24),
                Text(
                  'Drive with WADEXPRO',
                  style: Theme.of(context).textTheme.displayLarge?.copyWith(fontSize: 32, color: AppColors.primary),
                ),
                const SizedBox(height: 8),
                const Text(
                  'Your premium logistics journey starts here. Enter your phone number to continue.',
                  style: TextStyle(color: AppColors.textMuted, fontSize: 16),
                ),
                const SizedBox(height: 48),
                TextField(
                  controller: _phoneController,
                  keyboardType: TextInputType.phone,
                  style: const TextStyle(fontSize: 18, fontWeight: FontWeight.bold),
                  decoration: InputDecoration(
                    labelText: 'Phone Number',
                    hintText: '+233...',
                    prefixIcon: const Icon(Icons.phone),
                  ),
                ),
                const SizedBox(height: 24),
                ElevatedButton(
                  onPressed: isLoading
                      ? null
                      : () => ref.read(authProvider.notifier).login(_phoneController.text),
                  child: isLoading
                      ? const CircularProgressIndicator(color: Colors.white)
                      : const Text('Get Started'),
                ),

                if (AppConfig.googleAuthEnabled) ...[
                  const SizedBox(height: 16),
                  OutlinedButton.icon(
                    onPressed: isLoading ? null : () => ref.read(authProvider.notifier).signInWithGoogle(),
                    icon: Image.network(
                      'https://upload.wikimedia.org/wikipedia/commons/thumb/c/c1/Google_Color_Icon.svg/1200px-Google_Color_Icon.svg.png',
                      height: 22,
                      errorBuilder: (context, error, stackTrace) => const Icon(
                        Icons.g_mobiledata,
                        color: AppColors.primary,
                        size: 24,
                      ),
                    ),
                    label: const Text('Continue with Google', style: TextStyle(fontWeight: FontWeight.bold)),
                    style: OutlinedButton.styleFrom(
                      minimumSize: const Size(double.infinity, 56),
                      shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(16)),
                      side: BorderSide(color: Colors.grey.shade200),
                    ),
                  ),
                ],

                if (AppConfig.facebookAuthEnabled) ...[
                  const SizedBox(height: 16),
                  ElevatedButton.icon(
                    onPressed: isLoading ? null : () => ref.read(authProvider.notifier).signInWithFacebook(),
                    icon: const Icon(Icons.facebook, color: Colors.white, size: 28),
                    label: const Text('Continue with Facebook', style: TextStyle(fontWeight: FontWeight.bold, color: Colors.white)),
                    style: ElevatedButton.styleFrom(
                      backgroundColor: const Color(0xFF1877F2),
                      minimumSize: const Size(double.infinity, 56),
                      shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(16)),
                      elevation: 0,
                    ),
                  ),
                ],

                const SizedBox(height: 40),
                
                // Find My Account (Device Fingerprint Recovery)
                Center(
                  child: TextButton.icon(
                    onPressed: () {
                      ScaffoldMessenger.of(context).showSnackBar(
                        const SnackBar(
                          content: Text('Scanning device fingerprint for linked driver accounts...'),
                          backgroundColor: AppColors.primary,
                          duration: Duration(seconds: 3),
                        ),
                      );
                    },
                    icon: const Icon(Icons.screen_search_desktop_outlined, color: AppColors.textMuted, size: 20),
                    label: const Text(
                      'Lost access? Find my account',
                      style: TextStyle(
                        color: AppColors.textMuted,
                        decoration: TextDecoration.underline,
                        fontSize: 14,
                      ),
                    ),
                  ),
                ),
                const SizedBox(height: 40),
              ],
            ),
          ),
        ),
      ),
    );
  }
}
