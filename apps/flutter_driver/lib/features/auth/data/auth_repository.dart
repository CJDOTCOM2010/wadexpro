import 'package:dio/dio.dart';
import 'package:flutter_secure_storage/flutter_secure_storage.dart';
import '../../../core/network/api_client.dart';
import '../../../core/config/app_config.dart';

class AuthRepository {
  final ApiClient _apiClient;
  final _storage = const FlutterSecureStorage();

  AuthRepository(this._apiClient);

  Future<void> login(String phone) async {
    try {
      await _apiClient.instance.post('/auth/login/otp/send', data: {
        'phone': phone,
        'user_type': 'driver', 
      });
    } on DioException catch (e) {
      throw e.response?.data['message'] ?? 'Login failed';
    }
  }

  Future<Map<String, dynamic>> verifyOtp(String phone, String code) async {
    try {
      final response = await _apiClient.instance.post('/auth/login/otp', data: {
        'phone': phone,
        'code': code,
      });

      final result = response.data['data'];
      final tokens = result['tokens'];
      
      try {
        await Future.wait([
          _storage.write(key: 'access_token', value: tokens['access_token']),
          _storage.write(key: 'refresh_token', value: tokens['refresh_token']),
          _storage.write(key: 'driver_data', value: result['user'].toString()),
        ]).timeout(const Duration(seconds: 2));
      } catch (e) {
        print('SecureStorage write bypassed/timeout on Web Localhost: $e');
      }
      
      return result;
    } on DioException catch (e) {
      throw e.response?.data['message'] ?? 'OTP verification failed';
    }
  }

  Future<void> logout() async {
    try {
      await Future.wait([
        _storage.delete(key: 'access_token'),
        _storage.delete(key: 'driver_data'),
      ]).timeout(const Duration(seconds: 2));
    } catch (e) {
      print('SecureStorage delete bypassed/timeout: $e');
    }
  }

  Future<Map<String, dynamic>> loginWithOtp(String phone, String otp) async {
    try {
      final response = await _apiClient.instance.post('/auth/login', data: {
        'phone': phone,
        'otp': otp,
        'platform': AppConfig.currentPlatform,
        'app_version': AppConfig.currentAppVersion,
      });
      return response.data;
    } catch (e) {
      rethrow;
    }
  }

  Future<Map<String, dynamic>> signInWithGoogle(String idToken) async {
    try {
      final response = await _apiClient.instance.post('/auth/google/token', data: {
        'id_token': idToken,
        'user_type': 'driver',
        'platform': AppConfig.currentPlatform,
        'app_version': AppConfig.currentAppVersion,
      });
      return response.data;
    } catch (e) {
      rethrow;
    }
  }

  Future<Map<String, dynamic>> signInWithFacebook(String accessToken) async {
    try {
      final response = await _apiClient.instance.post('/auth/facebook/token', data: {
        'access_token': accessToken,
        'user_type': 'driver',
        'platform': AppConfig.currentPlatform,
        'app_version': AppConfig.currentAppVersion,
      });
      return response.data;
    } catch (e) {
      rethrow;
    }
  }

  Future<String?> getToken() async {
    try {
      return await _storage.read(key: 'access_token').timeout(const Duration(seconds: 2));
    } catch (e) {
      return null;
    }
  }
}
