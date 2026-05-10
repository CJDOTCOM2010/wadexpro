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

    if (response.statusCode == 200 && response.data['data'] != null) {
      final List slides = response.data['data'];
      return OnboardingConfig(
        pages: slides
            .map((s) => OnboardingPageConfig.fromJson(s, EnvironmentConfig.baseUrl))
            .toList(),
      );
    }
    
    throw Exception('Failed to load onboarding slides from server');
  } catch (e) {
    debugPrint('WADEXPRO: Remote onboarding fetch failed. $e');
    rethrow;
  }
});
