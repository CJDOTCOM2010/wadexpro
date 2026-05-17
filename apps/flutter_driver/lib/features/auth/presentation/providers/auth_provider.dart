import 'package:flutter/foundation.dart';
import 'package:google_sign_in/google_sign_in.dart';
import 'package:flutter_facebook_auth/flutter_facebook_auth.dart';
import 'package:flutter_riverpod/flutter_riverpod.dart';
import '../../../../core/config/app_config.dart';
import '../../data/auth_repository.dart';
import '../../../../core/network/providers.dart';
import '../../../../core/services/session_manager.dart';

enum AuthStatus {
  initial,
  loading,
  otpSent,
  authenticated,
  unauthenticated,
  error,
}

class AuthState {
  final AuthStatus status;
  final String? phone;
  final String? errorMessage;
  final Map<String, dynamic>? driver;

  AuthState({
    this.status = AuthStatus.initial,
    this.phone,
    this.errorMessage,
    this.driver,
  });

  AuthState copyWith({
    AuthStatus? status,
    String? phone,
    String? errorMessage,
    Map<String, dynamic>? driver,
    bool clearError = false,
  }) {
    return AuthState(
      status: status ?? this.status,
      phone: phone ?? this.phone,
      errorMessage: clearError ? null : (errorMessage ?? this.errorMessage),
      driver: driver ?? this.driver,
    );
  }
}

class AuthNotifier extends StateNotifier<AuthState> {
  final AuthRepository _repository;

  AuthNotifier(this._repository) : super(AuthState()) {
    _restoreSession();
  }

  Future<void> _restoreSession() async {
    try {
      final hasSession = await SessionManager.hasActiveSession();
      if (hasSession) {
        final driverId = await SessionManager.getDriverId();
        final driverPhone = await SessionManager.getDriverPhone();
        
        state = AuthState(
          status: AuthStatus.authenticated,
          phone: driverPhone,
          driver: driverId != null ? {'id': driverId} : null,
        );
        debugPrint('WADEXPRO: Session restored - user is logged in');
      }
    } catch (e) {
      debugPrint('WADEXPRO: Session restore failed: $e');
    }
  }

  Future<void> login(String phone) async {
    state = AuthState(status: AuthStatus.loading, phone: phone);
    try {
      await _repository.login(phone);
      state = AuthState(status: AuthStatus.otpSent, phone: phone);
    } catch (e) {
      state = AuthState(
        status: AuthStatus.error, 
        phone: phone,
        errorMessage: e.toString(),
      );
    }
  }

  Future<void> verifyOtp(String code) async {
    final currentPhone = state.phone;
    if (currentPhone == null) {
      state = AuthState(status: AuthStatus.error, errorMessage: 'Phone number missing. Please go back and re-enter.');
      return;
    }
    
    state = AuthState(status: AuthStatus.loading, phone: currentPhone);
    try {
      final data = await _repository.verifyOtp(currentPhone, code);
      
      // Extract tokens and user data
      final tokens = data['tokens'] as Map<String, dynamic>?;
      final accessToken = tokens?['access_token']?.toString();
      final refreshToken = tokens?['refresh_token']?.toString();
      
      // Extract user data — handle both nested and flat response shapes
      Map<String, dynamic>? userData;
      if (data['user'] is Map) {
        userData = Map<String, dynamic>.from(data['user'] as Map);
      } else {
        userData = {'phone': currentPhone, 'status': 'pending'};
      }
      
      print('WADEXPRO-DEBUG: OTP verified. User data: $userData');
      
      // Save session
      if (accessToken != null) {
        await SessionManager.saveSession(
          accessToken: accessToken,
          refreshToken: refreshToken,
          driverId: userData?['id']?.toString(),
          driverPhone: currentPhone,
        );
      }
      
      state = AuthState(
        status: AuthStatus.authenticated, 
        phone: currentPhone,
        driver: userData,
      );
    } catch (e) {
      print('WADEXPRO-DEBUG: OTP verification error: $e');
      state = AuthState(
        status: AuthStatus.error, 
        phone: currentPhone,
        errorMessage: e.toString(),
      );
    }
  }

  Future<void> signInWithGoogle() async {
    state = state.copyWith(status: AuthStatus.loading, clearError: true);
    try {
      final googleSignIn = GoogleSignIn(
        clientId: AppConfig.googleWebClientId,
      );
      
      final GoogleSignInAccount? googleUser = await googleSignIn.signIn();
      if (googleUser == null) {
        state = state.copyWith(status: AuthStatus.unauthenticated);
        return;
      }

      final GoogleSignInAuthentication googleAuth = await googleUser.authentication;
      final String? idToken = googleAuth.idToken;

      if (idToken == null) {
        throw Exception('Failed to obtain Google ID Token.');
      }

      final responseBody = await _repository.signInWithGoogle(idToken);
      
      final result = responseBody['data'] as Map<String, dynamic>? ?? responseBody;
      final tokens = result['tokens'] as Map<String, dynamic>?;

      if (tokens == null) {
        throw Exception('WADEXPRO Driver: Authentication succeeded but session sync failed.');
      }

      state = state.copyWith(status: AuthStatus.authenticated);
    } catch (e) {
      state = state.copyWith(
        status: AuthStatus.error,
        errorMessage: e.toString(),
      );
    }
  }

  Future<void> signInWithFacebook() async {
    state = state.copyWith(status: AuthStatus.loading, clearError: true);
    try {
      final LoginResult result = await FacebookAuth.instance.login(
        permissions: ['public_profile', 'email'],
      );

      if (result.status == LoginStatus.success) {
        final AccessToken? accessToken = result.accessToken;
        if (accessToken == null) throw Exception('Failed to obtain Facebook Access Token.');

        final responseBody = await _repository.signInWithFacebook(accessToken.tokenString);
        
        final resultData = responseBody['data'] as Map<String, dynamic>? ?? responseBody;
        final tokens = resultData['tokens'] as Map<String, dynamic>?;

        if (tokens == null) throw Exception('WADEXPRO Driver: Authentication succeeded but session sync failed.');

        state = state.copyWith(status: AuthStatus.authenticated);
      } else if (result.status == LoginStatus.cancelled) {
        state = state.copyWith(status: AuthStatus.unauthenticated);
      } else {
        throw Exception(result.message ?? 'Facebook login failed.');
      }
    } catch (e) {
      state = state.copyWith(
        status: AuthStatus.error,
        errorMessage: e.toString(),
      );
    }
  }

  Future<void> fetchProfile() async {
    if (state.status != AuthStatus.authenticated) return;
    try {
      final response = await _repository.getProfile();
      final userJson = response['data']?['user'] ?? response['user'];
      
      if (userJson != null) {
        state = state.copyWith(driver: Map<String, dynamic>.from(userJson));
      }
    } catch (e) {
      print('WADEXPRO: Fetch profile error: $e');
    }
  }

  Future<void> logout() async {
    await SessionManager.clearSession();
    state = AuthState(status: AuthStatus.unauthenticated);
  }
}

final authProvider = StateNotifierProvider<AuthNotifier, AuthState>((ref) {
  final repository = ref.watch(authRepositoryProvider);
  return AuthNotifier(repository);
});
