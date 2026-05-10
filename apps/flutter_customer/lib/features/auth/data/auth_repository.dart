import 'package:dio/dio.dart';
import '../../../core/network/api_client.dart';

class AuthRepository {
  final ApiClient _apiClient;

  AuthRepository(this._apiClient);

  /// Start login process by requesting an OTP.
  Future<Map<String, dynamic>> login(String phone) async {
    try {
      final response = await _apiClient.instance.post('/auth/login/otp/send', data: {
        'phone': phone,
        'user_type': 'customer',
      });
      return response.data;
    } on DioException catch (e) {
      throw e.response?.data['message'] ?? 'Failed to send OTP.';
    }
  }

  /// Verify OTP and complete login.
  Future<Map<String, dynamic>> verifyOtp(String phone, String code) async {
    try {
      final response = await _apiClient.instance.post('/auth/login/otp', data: {
        'phone': phone,
        'code': code,
      });
      return response.data;
    } on DioException catch (e) {
      throw e.response?.data['message'] ?? 'OTP verification failed.';
    }
  }

  /// Sign in with Google ID Token.
  Future<Map<String, dynamic>> signInWithGoogle(String idToken) async {
    try {
      final response = await _apiClient.instance.post('/auth/social/google', data: {
        'idToken': idToken,
      });
      return response.data;
    } on DioException catch (e) {
      throw e.response?.data['message'] ?? 'Google sign-in failed.';
    }
  }

  /// Sign in with Facebook Access Token.
  Future<Map<String, dynamic>> signInWithFacebook(String accessToken) async {
    try {
      final response = await _apiClient.instance.post('/auth/social/facebook', data: {
        'accessToken': accessToken,
      });
      return response.data;
    } on DioException catch (e) {
      throw e.response?.data['message'] ?? 'Facebook sign-in failed.';
    }
  }
}
