import 'package:flutter/material.dart';
import 'package:flutter_riverpod/flutter_riverpod.dart';
import 'core/theme/app_theme.dart';
import 'features/auth/presentation/providers/auth_provider.dart';
import 'features/auth/presentation/pages/login_screen.dart';
import 'features/home/presentation/pages/driver_home_screen.dart';
import 'features/onboarding/presentation/pages/splash_screen.dart';
import 'features/onboarding/presentation/pages/onboarding_screen.dart';

import 'features/auth/presentation/pages/otp_screen.dart';
import 'features/auth/presentation/pages/kyc_screen.dart';

import 'core/config/app_config.dart';
import 'core/config/brand_config.dart';
import 'core/theme/app_colors.dart';
import 'core/network/providers.dart';

import 'package:firebase_core/firebase_core.dart';
import 'package:firebase_messaging/firebase_messaging.dart';
import 'core/services/push_notification_service.dart';

void main() async {
  WidgetsFlutterBinding.ensureInitialized();
  
  // Initialize dynamic environment configuration
  await AppConfig.initialize(environment: Environment.dev);
  
  // Initialize Firebase Identity Services
  try {
    await Firebase.initializeApp();
    debugPrint('WADEXPRO Driver: Firebase Identity initialized successfully.');

    // Register background message handler
    FirebaseMessaging.onBackgroundMessage(firebaseMessagingBackgroundHandler);
  } catch (e) {
    debugPrint('WADEXPRO Driver: Firebase initialization skipped or failed: $e');
  }

  runApp(
    const ProviderScope(
      child: WadexproDriverApp(),
    ),
  );
}

class WadexproDriverApp extends ConsumerWidget {
  const WadexproDriverApp({super.key});

  @override
  Widget build(BuildContext context, WidgetRef ref) {
    return MaterialApp(
      title: BrandConfig.driverAppName,
      theme: AppTheme.light,
      debugShowCheckedModeBanner: false,
      home: SplashScreen(
        destinationBuilder: () => const VersionGate(child: _AppGate()),
      ),
    );
  }
}

class VersionGate extends ConsumerWidget {
  final Widget child;
  const VersionGate({super.key, required this.child});

  @override
  Widget build(BuildContext context, WidgetRef ref) {
    if (AppConfig.minAppVersion != null && AppConfig.currentAppVersion != null) {
      if (_isUpdateRequired(AppConfig.currentAppVersion!, AppConfig.minAppVersion!)) {
        return Scaffold(
          body: Container(
            padding: const EdgeInsets.all(32),
            color: Colors.white,
            child: Column(
              mainAxisAlignment: MainAxisAlignment.center,
              children: [
                const Icon(Icons.system_update_alt, size: 80, color: AppColors.primary),
                const SizedBox(height: 32),
                const Text(
                  'Driver App Update',
                  style: TextStyle(fontSize: 24, fontWeight: FontWeight.w900, color: AppColors.primary),
                ),
                const SizedBox(height: 16),
                Text(
                  'A critical update for ${BrandConfig.driverAppName} is available. Please update to version ${AppConfig.minAppVersion} to continue working.',
                  textAlign: TextAlign.center,
                  style: TextStyle(color: Colors.grey.shade600, height: 1.5),
                ),
                const SizedBox(height: 48),
                ElevatedButton(
                  onPressed: () {
                    // Launch store URL
                  },
                  style: ElevatedButton.styleFrom(
                    backgroundColor: AppColors.primary,
                    minimumSize: const Size(double.infinity, 56),
                    shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(16)),
                  ),
                  child: const Text('Update Now', style: TextStyle(color: Colors.white, fontWeight: FontWeight.bold)),
                ),
              ],
            ),
          ),
        );
      }
    }
    return child;
  }

  bool _isUpdateRequired(String current, String min) {
    try {
      List<int> currentParts = current.split('.').map(int.parse).toList();
      List<int> minParts = min.split('.').map(int.parse).toList();
      for (int i = 0; i < 3; i++) {
        if (currentParts[i] < minParts[i]) return true;
        if (currentParts[i] > minParts[i]) return false;
      }
    } catch (e) {
      return false;
    }
    return false;
  }
}

/// Gate: Onboarding → Login → Home
class _AppGate extends ConsumerWidget {
  const _AppGate();

  @override
  Widget build(BuildContext context, WidgetRef ref) {
    final hasSeenOnboarding = ref.watch(hasSeenOnboardingProvider);
    final authState = ref.watch(authProvider);

    if (!hasSeenOnboarding) {
      return OnboardingScreen(
        onComplete: () {
          Navigator.of(context).pushReplacement(
            MaterialPageRoute(builder: (_) => const VersionGate(child: _AuthGate())),
          );
        },
      );
    }

    return _buildAuthScreen(context, ref, authState);
  }

  Widget _buildAuthScreen(BuildContext context, WidgetRef ref, AuthState authState) {
    switch (authState.status) {
      case AuthStatus.authenticated:
        // Once authenticated, go straight to the home screen.
        // KYC onboarding is handled separately from the main auth gate.
        // Initialize push notifications for the driver
        Future.microtask(() {
          ref.read(pushNotificationServiceProvider).initialize();
        });
        return const DriverHomeScreen();
        
      case AuthStatus.otpSent:
        return const OtpScreen();
        
      case AuthStatus.error:
        // If phone is known, keep them on OTP screen so they can retry
        if (authState.phone != null) return const OtpScreen();
        return const LoginScreen();
        
      case AuthStatus.loading:
        if (authState.phone != null) {
          // Loading after OTP was sent — stay on OTP screen
          return const OtpScreen();
        }
        return const LoginScreen();
        
      case AuthStatus.initial:
      case AuthStatus.unauthenticated:
      default:
        return const LoginScreen();
    }
  }
}

class _AuthGate extends ConsumerWidget {
  const _AuthGate();

  @override
  Widget build(BuildContext context, WidgetRef ref) {
    final authState = ref.watch(authProvider);

    switch (authState.status) {
      case AuthStatus.authenticated:
        return const DriverHomeScreen();
        
      case AuthStatus.otpSent:
        return const OtpScreen();
        
      case AuthStatus.error:
        if (authState.phone != null) return const OtpScreen();
        return const LoginScreen();
        
      case AuthStatus.loading:
        if (authState.phone != null) return const OtpScreen();
        return const LoginScreen();
        
      case AuthStatus.initial:
      case AuthStatus.unauthenticated:
      default:
        return const LoginScreen();
    }
  }
}
