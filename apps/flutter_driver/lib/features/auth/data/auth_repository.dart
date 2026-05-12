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
        'user_type': 'driver',
      });

      final responseData = response.data;
      if (responseData == null || responseData['data'] == null) {
        throw 'Unexpected server response. Please try again.';
      }

      final result = Map<String, dynamic>.from(responseData['data'] as Map);
      final tokens = result['tokens'] as Map?;
      
      if (tokens != null) {
        try {
          await Future.wait([
            _storage.write(key: 'access_token', value: tokens['access_token']?.toString() ?? ''),
            _storage.write(key: 'refresh_token', value: tokens['refresh_token']?.toString() ?? ''),
            _storage.write(key: 'driver_data', value: result['user']?.toString() ?? ''),
          ]).timeout(const Duration(seconds: 2));
        } catch (e) {
          print('SecureStorage write bypassed/timeout: $e');
        }
      }
      
      return result;
    } on DioException catch (e) {
      final message = e.response?.data is Map 
          ? e.response?.data['message'] 
          : null;
      throw message ?? 'OTP verification failed. Please check your code and try again.';
    } catch (e) {
      if (e is String) rethrow;
      throw 'Connection error. Please check your internet and try again.';
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

  /// Get current driver profile information.
  Future<Map<String, dynamic>> getProfile() async {
    try {
      final response = await _apiClient.instance.get('/profile');
      return response.data;
    } on DioException catch (e) {
      throw e.response?.data['message'] ?? 'Failed to fetch profile.';
    }
  }

  /// Update driver profile information.
  Future<Map<String, dynamic>> updateProfile(Map<String, dynamic> data) async {
    try {
      final response = await _apiClient.instance.put('/profile/update', data: data);
      return response.data;
    } on DioException catch (e) {
      throw e.response?.data['message'] ?? 'Failed to update profile.';
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
