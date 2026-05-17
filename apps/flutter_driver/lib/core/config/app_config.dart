import 'package:dio/dio.dart';
import 'package:flutter/foundation.dart';
import 'package:package_info_plus/package_info_plus.dart';
import 'brand_config.dart';

enum Environment { dev, prod }

class AppConfig {
  final Environment environment;
  final String apiBaseUrl;
  final String socketUrl;

  AppConfig({
    required this.environment,
    required this.apiBaseUrl,
    required this.socketUrl,
  });

  static late AppConfig instance;

  // Remote Settings
  static bool googleAuthEnabled = false;
  static String? googleWebClientId;
  static String? googleIosClientId;

  static bool facebookAuthEnabled = false;
  static String? facebookAppId;
  static String? facebookClientToken;

  static String? minAppVersion;
  static String? downloadUrl;
  static String? currentAppVersion;
  static String? currentPlatform;

  // API Configuration from Super Admin Dashboard
  static String? remoteApiBaseUrl;
  static String? remoteSocketUrl;
  static int remoteTimeout = 30;
  static int remoteRetryAttempts = 3;

  static Future<void> initialize({required Environment environment}) async {
    // Determine initial API URL - use production for real devices, check for API config override
    String initialBaseUrl;
    String initialSocketUrl;
    
    if (environment == Environment.prod || !kIsWeb) {
      // For production or real devices, use the configured live server URL
      // The actual API endpoints will be fetched from Super Admin Dashboard config
      initialBaseUrl = 'https://wadexpro-4rexnj1k.on-forge.com/api/v1';
      initialSocketUrl = 'https://wadexpro-4rexnj1k.on-forge.com:3000';
    } else {
      // For emulator/web dev
      final String host = kIsWeb ? 'wadexpro.test' : '10.0.2.2';
      final String protocol = kIsWeb ? 'https' : 'http';
      final String port = kIsWeb ? '' : ':8000';
      initialBaseUrl = '$protocol://$host$port/api/v1';
      initialSocketUrl = 'http://$host:3002';
    }

    instance = AppConfig(
      environment: environment,
      apiBaseUrl: initialBaseUrl,
      socketUrl: initialSocketUrl,
    );

    try {
      final dio = Dio(BaseOptions(
        connectTimeout: const Duration(seconds: 10),
        receiveTimeout: const Duration(seconds: 10),
      ));
      
      final response = await dio.get('${instance.apiBaseUrl}/auth/config');
      
      if (response.statusCode == 200) {
        final data = response.data['data'] ?? response.data;
        bool parseBool(dynamic value) => value == true || value == 'true' || value == '1' || value == 1;

        bool globalGoogle = parseBool(data['google_auth_enabled']);
        if (globalGoogle) {
          if (UniversalPlatform.isAndroid) {
            googleAuthEnabled = parseBool(data['google_auth_android'] ?? true);
          } else if (UniversalPlatform.isIOS) {
            googleAuthEnabled = parseBool(data['google_auth_ios'] ?? true);
          } else {
            googleAuthEnabled = parseBool(data['google_auth_web'] ?? true);
          }
        } else {
            googleAuthEnabled = false;
        }

        googleWebClientId = data['google_auth_web_client_id'];
        googleIosClientId = data['google_auth_ios_client_id'];

        bool globalFacebook = parseBool(data['facebook_auth_enabled']);
        if (globalFacebook) {
          if (UniversalPlatform.isAndroid) {
            facebookAuthEnabled = parseBool(data['facebook_auth_android'] ?? true);
          } else if (UniversalPlatform.isIOS) {
            facebookAuthEnabled = parseBool(data['facebook_auth_ios'] ?? true);
          } else {
            facebookAuthEnabled = parseBool(data['facebook_auth_web'] ?? true);
          }
        } else {
            facebookAuthEnabled = false;
        }

        final manifest = data['manifest'];
        if (manifest != null) {
          minAppVersion = manifest['min_driver_version'];
          
          if (UniversalPlatform.isAndroid) {
            downloadUrl = manifest['driver_play_store'];
            currentPlatform = 'android';
          } else if (UniversalPlatform.isIOS) {
            downloadUrl = manifest['driver_app_store'];
            currentPlatform = 'ios';
          } else {
            currentPlatform = 'web';
          }
        }

        // --- Dynamic Branding from Super Admin Dashboard ---
        final branding = data['branding'];
        BrandConfig.fromJson(branding);

        // --- API Configuration from Super Admin Dashboard ---
        final apiConfig = data['api_configuration'];
        if (apiConfig != null) {
          remoteApiBaseUrl = apiConfig['api_driver_base_url'];
          remoteSocketUrl = apiConfig['api_driver_socket_url'];
          remoteTimeout = int.tryParse(apiConfig['api_platform_timeout']?.toString() ?? '30') ?? 30;
          remoteRetryAttempts = int.tryParse(apiConfig['api_platform_retry_attempts']?.toString() ?? '3') ?? 3;
          
          // Update instance with remote config if available
          if (remoteApiBaseUrl != null && remoteSocketUrl != null) {
            instance = AppConfig(
              environment: environment,
              apiBaseUrl: remoteApiBaseUrl!,
              socketUrl: remoteSocketUrl!,
            );
            debugPrint('WADEXPRO: Using remote API configuration from Super Admin');
          }
        }
      }

      final packageInfo = await PackageInfo.fromPlatform();
      currentAppVersion = packageInfo.version;
      debugPrint('WADEXPRO: Dynamic configuration sync successful. Version: $currentAppVersion');
    } catch (e) {
      debugPrint('WADEXPRO: Dynamic configuration sync failed. Using fallback. $e');
    }
  }

  /// Refresh configuration from server (can be called at runtime)
  static Future<void> refreshConfig() async {
    debugPrint('WADEXPRO: Refreshing configuration from server...');
    await initialize(environment: instance.environment);
  }
}

class UniversalPlatform {
  static bool get isAndroid => !kIsWeb && defaultTargetPlatform == TargetPlatform.android;
  static bool get isIOS => !kIsWeb && defaultTargetPlatform == TargetPlatform.iOS;
}
