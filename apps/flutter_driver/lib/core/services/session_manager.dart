import 'package:flutter_secure_storage/flutter_secure_storage.dart';

class SessionManager {
  static const _storage = FlutterSecureStorage();
  
  // Storage keys
  static const String keyOnboardingSeen = 'onboarding_seen';
  static const String keyAccessToken = 'access_token';
  static const String keyRefreshToken = 'refresh_token';
  static const String keyDriverId = 'driver_id';
  static const String keyDriverPhone = 'driver_phone';

  // Check if onboarding has been seen
  static Future<bool> hasSeenOnboarding() async {
    final value = await _storage.read(key: keyOnboardingSeen);
    return value == 'true';
  }

  // Mark onboarding as seen
  static Future<void> setOnboardingSeen(bool seen) async {
    await _storage.write(key: keyOnboardingSeen, value: seen.toString());
  }

  // Check if user has valid session
  static Future<bool> hasActiveSession() async {
    final token = await _storage.read(key: keyAccessToken);
    return token != null && token.isNotEmpty;
  }

  // Get stored access token
  static Future<String?> getAccessToken() async {
    return await _storage.read(key: keyAccessToken);
  }

  // Save session data after login
  static Future<void> saveSession({
    required String accessToken,
    String? refreshToken,
    String? driverId,
    String? driverPhone,
  }) async {
    await _storage.write(key: keyAccessToken, value: accessToken);
    if (refreshToken != null) {
      await _storage.write(key: keyRefreshToken, value: refreshToken);
    }
    if (driverId != null) {
      await _storage.write(key: keyDriverId, value: driverId);
    }
    if (driverPhone != null) {
      await _storage.write(key: keyDriverPhone, value: driverPhone);
    }
  }

  // Clear session on logout
  static Future<void> clearSession() async {
    await _storage.delete(key: keyAccessToken);
    await _storage.delete(key: keyRefreshToken);
    await _storage.delete(key: keyDriverId);
    await _storage.delete(key: keyDriverPhone);
  }

  // Get stored driver ID
  static Future<String?> getDriverId() async {
    return await _storage.read(key: keyDriverId);
  }

  // Get stored driver phone
  static Future<String?> getDriverPhone() async {
    return await _storage.read(key: keyDriverPhone);
  }
}