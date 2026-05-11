import 'package:flutter_riverpod/flutter_riverpod.dart';
import '../models/splash_config.dart';
import '../network/api_provider.dart';
import '../config/environment_config.dart';

/// Fetches the global application configuration (such as company logo) directly from the dashboard/admin panel API.
final appConfigProvider = FutureProvider<SplashConfig?>((ref) async {
  try {
    final apiClient = ref.read(apiClientProvider);
    final response = await apiClient.instance.get('/splash-config');
    
    if (response.statusCode == 200 && response.data != null) {
      return SplashConfig.fromJson(response.data, EnvironmentConfig.baseUrl);
    }
  } catch (e) {
    // Return null if configuration fails to load, allowing UI to fallback gracefully.
  }
  return null;
});
