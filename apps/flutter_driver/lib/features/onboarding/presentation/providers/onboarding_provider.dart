import 'package:flutter/material.dart';
import 'package:flutter_riverpod/flutter_riverpod.dart';
import '../../../../core/models/onboarding_config.dart';
import '../../../../core/network/providers.dart';
import '../../../../core/config/environment_config.dart';

/// Fetches onboarding slides from the API, falling back to hardcoded defaults.
final onboardingConfigProvider = FutureProvider<OnboardingConfig>((ref) async {
  try {
    final apiClient = ref.read(apiClientProvider);
    final response = await apiClient.instance.get('/onboarding/driver');

    if (response.statusCode == 200 && response.data['data'] != null && (response.data['data'] as List).isNotEmpty) {
      final List slides = response.data['data'];
      return OnboardingConfig(
        pages: slides
            .map((s) => OnboardingPageConfig.fromJson(s, EnvironmentConfig.baseUrl))
            .toList(),
        getStartedText: 'Start Driving',
      );
    }
    
    return _getFallbackConfig();
  } catch (e) {
    debugPrint('WADEXPRO: Remote onboarding fetch failed for driver. $e');
    return _getFallbackConfig();
  }
});

OnboardingConfig _getFallbackConfig() {
  return OnboardingConfig(
    pages: [
      const OnboardingPageConfig(
        title: 'Earn with WADEXPRO',
        description: 'Flexible hours, premium rates, and a professional driver support network.',
        imagePath: 'https://images.unsplash.com/photo-1449965408869-eaa3f722e40d?q=80&w=2070&auto=format&fit=crop',
        isNetworkImage: true,
      ),
      const OnboardingPageConfig(
        title: 'Professional Tools',
        description: 'Advanced mapping and real-time earnings tracking at your fingertips.',
        imagePath: 'https://images.unsplash.com/photo-1580273916550-e323be2ae537?q=80&w=2064&auto=format&fit=crop',
        isNetworkImage: true,
      ),
    ],
    getStartedText: 'Start Driving',
  );
}
