import 'package:dio/dio.dart';
import 'package:flutter/foundation.dart';

class RemoteConfigService {
  static final RemoteConfigService _instance = RemoteConfigService._internal();
  factory RemoteConfigService() => _instance;
  RemoteConfigService._internal();

  Map<String, dynamic>? _cachedConfig;
  DateTime? _lastFetchTime;
  static const Duration _cacheExpiry = Duration(minutes: 5);

  // Fallback defaults - used when backend is unreachable
  static const String _defaultBaseUrl = 'https://wadexpro-4rexnj1k.on-forge.com/api/v1';
  static const String _defaultSocketUrl = 'https://wadexpro-4rexnj1k.on-forge.com:3000';
  static const int _defaultTimeout = 30;
  static const int _defaultRetryAttempts = 3;

  String get baseUrl {
    if (_cachedConfig != null && _cachedConfig!['api_driver_base_url'] != null) {
      return _cachedConfig!['api_driver_base_url'] as String;
    }
    return _defaultBaseUrl;
  }

  String get socketUrl {
    if (_cachedConfig != null && _cachedConfig!['api_driver_socket_url'] != null) {
      return _cachedConfig!['api_driver_socket_url'] as String;
    }
    return _defaultSocketUrl;
  }

  int get timeout {
    if (_cachedConfig != null && _cachedConfig!['api_platform_timeout'] != null) {
      return int.tryParse(_cachedConfig!['api_platform_timeout'].toString()) ?? _defaultTimeout;
    }
    return _defaultTimeout;
  }

  int get retryAttempts {
    if (_cachedConfig != null && _cachedConfig!['api_platform_retry_attempts'] != null) {
      return int.tryParse(_cachedConfig!['api_platform_retry_attempts'].toString()) ?? _defaultRetryAttempts;
    }
    return _defaultRetryAttempts;
  }

  bool get isLoaded => _cachedConfig != null;

  Future<void> loadConfig() async {
    // Check if cache is still valid
    if (_cachedConfig != null && _lastFetchTime != null) {
      final elapsed = DateTime.now().difference(_lastFetchTime!);
      if (elapsed < _cacheExpiry) {
        debugPrint('RemoteConfig: Using cached config');
        return;
      }
    }

    try {
      // Try to fetch from backend - use a simple approach without complex auth
      final dio = Dio(BaseOptions(
        connectTimeout: const Duration(seconds: 5),
        receiveTimeout: const Duration(seconds: 5),
      ));

      // Try the known production endpoint first
      String configUrl = '$_defaultBaseUrl/settings/public';

      final response = await dio.get(configUrl);

      if (response.statusCode == 200 && response.data != null) {
        final data = response.data;
        if (data is Map && data['data'] != null) {
          _cachedConfig = Map<String, dynamic>.from(data['data']);
          _lastFetchTime = DateTime.now();
          debugPrint('RemoteConfig: Successfully loaded from backend');
        }
      }
    } catch (e) {
      debugPrint('RemoteConfig: Failed to load from backend: $e');
      // Continue using fallback values
    }
  }

  Future<void> refreshConfig() async {
    _cachedConfig = null;
    _lastFetchTime = null;
    await loadConfig();
  }
}

final remoteConfigService = RemoteConfigService();