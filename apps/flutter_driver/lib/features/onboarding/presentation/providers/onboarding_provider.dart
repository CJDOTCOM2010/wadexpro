import 'package:flutter/foundation.dart';
import 'package:flutter_riverpod/flutter_riverpod.dart';
import '../../../../core/models/onboarding_config.dart';
import '../../../../core/network/providers.dart';
import '../../../../core/config/environment_config.dart';

/// Fetches onboarding slides from the API.
/// Returns empty pages when no slides are configured — the screen handles
/// this by auto-completing to the next gate.
final onboardingConfigProvider = FutureProvider<OnboardingConfig>((ref) async {
  try {
    final apiClient = ref.read(apiClientProvider);
    final response = await apiClient.instance.get('/onboarding/driver');

    if (response.statusCode == 200 && response.data['data'] != null) {
      final List slides = response.data['data'];
      return OnboardingConfig(
        pages: slides
            .map((s) => OnboardingPageConfig.fromJson(s, EnvironmentConfig.baseUrl))
            .toList(),
        getStartedText: 'Start Driving',
      );
    }

    return const OnboardingConfig(pages: [], getStartedText: 'Start Driving');
  } catch (e) {
    debugPrint('WADEXPRO: Remote onboarding fetch failed for driver. $e');
    return const OnboardingConfig(pages: [], getStartedText: 'Start Driving');
  }
});
