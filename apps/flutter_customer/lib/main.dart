import 'package:flutter/material.dart';
import 'package:flutter_riverpod/flutter_riverpod.dart';
import 'core/theme/app_theme.dart';
import 'core/theme/theme_provider.dart';
import 'features/auth/presentation/pages/login_screen.dart';
import 'features/auth/presentation/providers/auth_provider.dart';
import 'features/auth/presentation/providers/auth_state.dart';
import 'features/home/presentation/pages/main_dashboard_screen.dart';
import 'features/onboarding/presentation/pages/splash_screen.dart';
import 'features/onboarding/presentation/pages/onboarding_screen.dart';
import 'core/network/api_provider.dart';
import 'core/config/app_config.dart';
import 'core/theme/app_colors.dart';
import 'core/config/brand_config.dart';

import 'features/auth/presentation/pages/otp_screen.dart';

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
    debugPrint('WADEXPRO: Firebase Identity initialized successfully.');

    // Register background message handler
    FirebaseMessaging.onBackgroundMessage(firebaseMessagingBackgroundHandler);
  } catch (e) {
    debugPrint('WADEXPRO: Firebase initialization skipped or failed: $e');
  }

  runApp(
    const ProviderScope(
      child: MyApp(),
    ),
  );
}

class MyApp extends ConsumerWidget {
  const MyApp({super.key});

  @override
  Widget build(BuildContext context, WidgetRef ref) {
    final themeMode = ref.watch(themeModeProvider);

    return MaterialApp(
      title: BrandConfig.customerAppName,
      debugShowCheckedModeBanner: false,
      theme: AppTheme.light,
      darkTheme: AppTheme.dark,
      themeMode: themeMode,
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
                const Icon(Icons.system_update_alt, size: 80, color: AppColors.primaryNavy),
                const SizedBox(height: 32),
                const Text(
                  'Update Required',
                  style: TextStyle(fontSize: 24, fontWeight: FontWeight.w900, color: AppColors.primaryNavy),
                ),
                const SizedBox(height: 16),
                Text(
                  'A new version of ${BrandConfig.appName} is available. Please update to version ${AppConfig.minAppVersion} to continue.',
                  textAlign: TextAlign.center,
                  style: TextStyle(color: Colors.grey.shade600, height: 1.5),
                ),
                const SizedBox(height: 48),
                ElevatedButton(
                  onPressed: () {
                    // Launch store URL
                  },
                  style: ElevatedButton.styleFrom(
                    backgroundColor: AppColors.primaryNavy,
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
    List<int> currentParts = current.split('.').map(int.parse).toList();
    List<int> minParts = min.split('.').map(int.parse).toList();
    for (int i = 0; i < 3; i++) {
      if (currentParts[i] < minParts[i]) return true;
      if (currentParts[i] > minParts[i]) return false;
    }
    return false;
  }
}

/// Gate that decides: Onboarding → Login → Home
class _AppGate extends ConsumerWidget {
  const _AppGate();

  @override
  Widget build(BuildContext context, WidgetRef ref) {
    final hasSeenOnboarding = ref.watch(hasSeenOnboardingProvider);
    final authState = ref.watch(authProvider);

    // WADEX-Guard: Smart Routing Engine
    // 1. If user is authenticated, always take them to Home immediately
    if (authState.status == AuthStatus.authenticated) {
      // Connect Socket if authenticated
      Future.microtask(() {
        ref.read(socketServiceProvider).connect(AppConfig.instance.socketUrl, '/rider');
        // Initialize push notifications
        ref.read(pushNotificationServiceProvider).initialize();
      });
      return const MainDashboardScreen();
    }

    // 2. If session is still loading, wait on splash (handled by SplashScreen, but guard here too)
    if (authState.status == AuthStatus.loading) {
      return const Scaffold(body: Center(child: CircularProgressIndicator()));
    }

    // 3. If they haven't seen onboarding and aren't logged in, show onboarding
    if (!hasSeenOnboarding) {
      return OnboardingScreen(
        onComplete: () {
          Navigator.of(context).pushReplacement(
            MaterialPageRoute(builder: (_) => const VersionGate(child: _AuthGate())),
          );
        },
      );
    }

    // 4. Otherwise, handle normal auth flows (Login, OTP, Error)
    return _buildAuthHome(authState);
  }

  Widget _buildAuthHome(AuthState authState) {
    switch (authState.status) {
      case AuthStatus.authenticated:
        return const MainDashboardScreen();
      case AuthStatus.loading:
        return const Scaffold(body: Center(child: CircularProgressIndicator()));
      case AuthStatus.codeSent:
        return OtpScreen(identifier: authState.identifier ?? '');
      case AuthStatus.error:
        if (authState.identifier != null) return OtpScreen(identifier: authState.identifier!);
        return const LoginScreen();
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

    ref.listen(authProvider, (previous, next) {
      if (next.status == AuthStatus.authenticated && previous?.status != AuthStatus.authenticated) {
        ref.read(socketServiceProvider).connect(AppConfig.instance.socketUrl, '/rider');
      }
    });

    switch (authState.status) {
      case AuthStatus.authenticated:
        return const MainDashboardScreen();
      case AuthStatus.loading:
        return const Scaffold(body: Center(child: CircularProgressIndicator()));
      case AuthStatus.codeSent:
        return OtpScreen(identifier: authState.identifier ?? '');
      case AuthStatus.error:
        if (authState.identifier != null) return OtpScreen(identifier: authState.identifier!);
        return const LoginScreen();
      default:
        return const LoginScreen();
    }
  }
}
