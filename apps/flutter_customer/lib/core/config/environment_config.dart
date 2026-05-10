import 'package:flutter/foundation.dart';

enum Environment { develop, staging, production }

class EnvironmentConfig {
  static const Environment environment = kDebugMode ? Environment.develop : Environment.production;

  static String get baseUrl {
    switch (environment) {
      case Environment.develop:
        // WADEX-Guard: Select network gateway based on platform context
        final String host = kIsWeb ? 'wadexpro.test' : '10.0.2.2';
        final String protocol = kIsWeb ? 'https' : 'http';
        final String port = kIsWeb ? '' : ':8000';
        return '$protocol://$host$port/api/v1';
      case Environment.staging:
        return 'https://staging-api.wadexpro.com/api/v1';
      case Environment.production:
        return 'https://api.wadexpro.com/api/v1';
    }
  }

  static String get socketUrl {
    switch (environment) {
      case Environment.develop:
        return 'http://localhost:3000';
      case Environment.staging:
        return 'https://staging-ws.wadexpro.com';
      case Environment.production:
        return 'https://ws.wadexpro.com';
    }
  }

  static bool get isProduction => environment == Environment.production;
}
