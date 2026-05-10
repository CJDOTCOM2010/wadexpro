import 'package:flutter/foundation.dart';

enum Environment { develop, staging, production }

class EnvironmentConfig {
  static const Environment environment = kDebugMode ? Environment.develop : Environment.production;

  static String get baseUrl {
    switch (environment) {
      case Environment.develop:
        // WADEX-Guard: In production-parity mode, we route development traffic to the live gateway
        final String host = 'wadexpro-4rexnj1k.on-forge.com';
        final String protocol = 'https';
        return '$protocol://$host/api/v1';
      case Environment.staging:
        return 'https://staging-api.wadexpro.com/api/v1';
      case Environment.production:
        return 'https://wadexpro-4rexnj1k.on-forge.com/api/v1';
    }
  }

  static String get socketUrl {
    switch (environment) {
      case Environment.develop:
        return 'https://wadexpro-4rexnj1k.on-forge.com:3000';
      case Environment.staging:
        return 'https://staging-ws.wadexpro.com';
      case Environment.production:
        return 'https://wadexpro-4rexnj1k.on-forge.com:3000';
    }
  }

  static bool get isProduction => environment == Environment.production;
}
