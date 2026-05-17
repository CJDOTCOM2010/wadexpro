import 'package:dio/dio.dart';
import 'package:flutter_secure_storage/flutter_secure_storage.dart';

import '../config/app_config.dart';

class ApiClient {
  final Dio _dio = Dio();
  final _storage = const FlutterSecureStorage();
  
  ApiClient() {
    // Use AppConfig for dynamic API URL from Super Admin Dashboard
    _dio.options.baseUrl = AppConfig.instance.apiBaseUrl;
    _dio.options.connectTimeout = Duration(seconds: AppConfig.remoteTimeout);
    _dio.options.receiveTimeout = Duration(seconds: AppConfig.remoteTimeout);

    _dio.interceptors.add(InterceptorsWrapper(
      onRequest: (options, handler) async {
        String? token;
        try {
          // WADEX-Guard: Prevent FlutterSecureStorage from hanging infinite on Web without HTTPS
          token = await _storage.read(key: 'access_token').timeout(const Duration(seconds: 2));
        } catch (e) {
          print('SecureStorage read bypassed/timeout: $e');
        }

        if (token != null) {
          options.headers['Authorization'] = 'Bearer $token';
        }
        options.headers['Accept'] = 'application/json';
        return handler.next(options);
      },
      onError: (e, handler) {
        // WADEX-Guard: Standardized JSON error parsing
        if (e.response?.data is Map) {
          final data = e.response!.data;
          final code = data['code'];
          final message = data['message'];

          if (code == 'GATEWAY_GUARD_ACTIVE' || code == 'SYS_RESILIENCE_01') {
            print('WADEX-Guard Interception: Throttling or system resilience active.');
            // Transform to a user-friendly message for the UI
            e = DioException(
              requestOptions: e.requestOptions,
              response: e.response,
              type: e.type,
              error: 'WADEX Service is stabilizing. Please wait a few seconds and try again.',
            );
          } else if (message != null) {
             print('API Error: $message (Code: $code)');
          }
        }
        return handler.next(e);
      },
    ));
  }

  Dio get instance => _dio;
}
