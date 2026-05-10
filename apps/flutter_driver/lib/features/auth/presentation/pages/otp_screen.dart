import 'package:flutter/material.dart';
import 'package:flutter_riverpod/flutter_riverpod.dart';
import '../providers/auth_provider.dart';
import '../../../../core/theme/app_colors.dart';

class OtpScreen extends ConsumerStatefulWidget {
  const OtpScreen({super.key});

  @override
  ConsumerState<OtpScreen> createState() => _OtpScreenState();
}

class _OtpScreenState extends ConsumerState<OtpScreen> {
  final _otpController = TextEditingController();

  @override
  Widget build(BuildContext context) {
    final authState = ref.watch(authProvider);

    // AppGate handles navigation on authenticated — we only show errors here
    ref.listen(authProvider, (previous, next) {
      if (next.status == AuthStatus.error) {
        ScaffoldMessenger.of(context).showSnackBar(
          SnackBar(
            content: Text(next.errorMessage ?? 'Verification failed. Please try again.'),
            backgroundColor: Colors.red.shade700,
            duration: const Duration(seconds: 4),
          ),
        );
      }
    });

    return Scaffold(
      appBar: AppBar(backgroundColor: Colors.transparent, elevation: 0),
      body: Padding(
        padding: const EdgeInsets.symmetric(horizontal: 24.0),
        child: Column(
          crossAxisAlignment: CrossAxisAlignment.start,
          children: [
            const Icon(Icons.security, size: 64, color: AppColors.primary),
            const SizedBox(height: 24),
            Text(
              'Verification',
              style: Theme.of(context).textTheme.displayLarge?.copyWith(fontSize: 32, color: AppColors.primary),
            ),
            const SizedBox(height: 8),
            Text(
              'A 6-digit code has been sent to ${authState.phone}. Enter it below to secure your driver session.',
              style: const TextStyle(color: AppColors.textMuted, fontSize: 16),
            ),
            const SizedBox(height: 48),
            TextField(
              controller: _otpController,
              keyboardType: TextInputType.number,
              style: const TextStyle(fontSize: 24, fontWeight: FontWeight.bold, letterSpacing: 8),
              textAlign: TextAlign.center,
              decoration: const InputDecoration(
                hintText: '000000',
              ),
            ),
            const SizedBox(height: 32),
            ElevatedButton(
              onPressed: authState.status == AuthStatus.loading
                  ? null
                  : () => ref.read(authProvider.notifier).verifyOtp(_otpController.text),
              child: authState.status == AuthStatus.loading
                  ? const CircularProgressIndicator(color: Colors.white)
                  : const Text('Verify & Finish'),
            ),
          ],
        ),
      ),
    );
  }
}
