import 'package:dio/dio.dart';
import 'package:flutter_secure_storage/flutter_secure_storage.dart';
import '../config/environment_config.dart';

class ApiClient {
  final Dio _dio;
  final FlutterSecureStorage _storage = const FlutterSecureStorage();

  String _locale = 'en';

  ApiClient(this._dio) {
    _dio.options.baseUrl = EnvironmentConfig.baseUrl; // Dynamic environment gateway
    _dio.options.connectTimeout = const Duration(seconds: 10);
    _dio.options.receiveTimeout = const Duration(seconds: 10);
    
    _dio.interceptors.add(InterceptorsWrapper(
      onRequest: (options, handler) async {
        options.headers['Accept-Language'] = _locale;
        String? token;
        try {
          // WADEX-Guard: Secure storage can hang on Web HTTP localhost. Fail-safed.
          token = await _storage.read(key: 'access_token').timeout(const Duration(seconds: 2));
        } catch (e) {
          print('SecureStorage read failed: $e');
        }
        
        if (token != null) {
          options.headers['Authorization'] = 'Bearer $token';
        }
        return handler.next(options);
      },
      onError: (DioException e, handler) async {
        // WADEX-Guard: Parse standardized backend error codes
        if (e.response?.data is Map) {
          final data = e.response!.data;
          final code = data['code'];
          final message = data['message'];

          if (code == 'GATEWAY_GUARD_ACTIVE' || code == 'SYS_ERR') {
            print('WADEX-Guard: System resilience active — $message');
            e = DioException(
              requestOptions: e.requestOptions,
              response: e.response,
              type: e.type,
              error: 'Service is stabilizing. Please wait a moment and try again.',
            );
            return handler.next(e);
          }
        }

        // Token refresh on 401
        if (e.response?.statusCode == 401) {
          // Trigger refresh token logic here if a refresh token exists
          final refreshToken = await _storage.read(key: 'refresh_token');
          if (refreshToken != null) {
            try {
              final response = await _dio.post('/auth/refresh', data: {
                'refresh_token': refreshToken,
              });
              
              final newAccessToken = response.data['access_token'];
              final newRefreshToken = response.data['refresh_token'];
              
              await _storage.write(key: 'access_token', value: newAccessToken);
              await _storage.write(key: 'refresh_token', value: newRefreshToken);
              
              // Retry the original request
              final options = e.requestOptions;
              options.headers['Authorization'] = 'Bearer $newAccessToken';
              final retryResponse = await _dio.fetch(options);
              return handler.resolve(retryResponse);
            } catch (err) {
              // Refresh failed, logout user
              await _storage.deleteAll();
            }
          }
        }
        return handler.next(e);
      },
    ));
  }

  void setLocale(String locale) {
    _locale = locale;
  }

  Dio get instance => _dio;
}
