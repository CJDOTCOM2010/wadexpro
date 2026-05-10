import 'package:flutter/material.dart';
import 'package:flutter_riverpod/flutter_riverpod.dart';
import '../../../../core/models/onboarding_config.dart';
import '../../../../core/network/api_provider.dart';
import '../../../../core/config/environment_config.dart';

/// Fetches onboarding slides from the API, falling back to hardcoded defaults.
final onboardingConfigProvider = FutureProvider<OnboardingConfig>((ref) async {
  try {
    final apiClient = ref.read(apiClientProvider);
    final response = await apiClient.instance.get('/onboarding/customer');

    if (response.statusCode == 200 && response.data['data'] != null && (response.data['data'] as List).isNotEmpty) {
      final List slides = response.data['data'];
      return OnboardingConfig(
        pages: slides
            .map((s) => OnboardingPageConfig.fromJson(s, EnvironmentConfig.baseUrl))
            .toList(),
      );
    }
    
    // WADEX-Guard: Return local fallback if server is empty or unreachable
    return _getFallbackConfig();
  } catch (e) {
    debugPrint('WADEXPRO: Remote onboarding fetch failed, using local fallback. $e');
    return _getFallbackConfig();
  }
});

OnboardingConfig _getFallbackConfig() {
  return OnboardingConfig(
    pages: [
      const OnboardingPageConfig(
        title: 'Welcome to WADEXPRO',
        description: 'Premium logistics and transport solutions tailored for Ghana.',
        imagePath: 'https://images.unsplash.com/photo-1519003722824-194d4455a60c?q=80&w=2075&auto=format&fit=crop',
        isNetworkImage: true,
      ),
      const OnboardingPageConfig(
        title: 'Safe & Secure',
        description: 'Real-time tracking and verified drivers for your peace of mind.',
        imagePath: 'https://images.unsplash.com/photo-1501700493788-fa1a4fc9fe62?q=80&w=2040&auto=format&fit=crop',
        isNetworkImage: true,
      ),
    ],
  );
}
