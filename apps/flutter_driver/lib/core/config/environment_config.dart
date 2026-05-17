import 'package:flutter/foundation.dart';
import 'remote_config_service.dart';

enum Environment { develop, staging, production }

class EnvironmentConfig {
  static const Environment environment = kDebugMode ? Environment.develop : Environment.production;

  // Initialize remote config on first access
  static bool _initialized = false;

  static Future<void> initialize() async {
    if (!_initialized) {
      await remoteConfigService.loadConfig();
      _initialized = true;
    }
  }

  static String get baseUrl {
    if (remoteConfigService.isLoaded) {
      return remoteConfigService.baseUrl;
    }
    // Fallback to hardcoded values if remote config not loaded
    switch (environment) {
      case Environment.develop:
        return 'https://wadexpro-4rexnj1k.on-forge.com/api/v1';
      case Environment.staging:
        return 'https://staging-api.wadexpro.com/api/v1';
      case Environment.production:
        return 'https://wadexpro-4rexnj1k.on-forge.com/api/v1';
    }
  }

  static String get socketUrl {
    if (remoteConfigService.isLoaded) {
      return remoteConfigService.socketUrl;
    }
    // Fallback to hardcoded values if remote config not loaded
    switch (environment) {
      case Environment.develop:
        return 'https://wadexpro-4rexnj1k.on-forge.com:3000';
      case Environment.staging:
        return 'https://staging-ws.wadexpro.com';
      case Environment.production:
        return 'https://wadexpro-4rexnj1k.on-forge.com:3000';
    }
  }

  static int get timeout => remoteConfigService.isLoaded ? remoteConfigService.timeout : 30;

  static int get retryAttempts => remoteConfigService.isLoaded ? remoteConfigService.retryAttempts : 3;

  static bool get isProduction => environment == Environment.production;
}
