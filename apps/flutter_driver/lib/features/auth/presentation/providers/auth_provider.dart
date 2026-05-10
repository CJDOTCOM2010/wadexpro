import 'package:google_sign_in/google_sign_in.dart';
import 'package:flutter_facebook_auth/flutter_facebook_auth.dart';
import 'package:flutter_riverpod/flutter_riverpod.dart';
import '../../../../core/config/app_config.dart';
import '../../data/auth_repository.dart';
import '../../../../core/network/providers.dart';

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
  final dynamic driver;

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
    dynamic driver,
  }) {
    return AuthState(
      status: status ?? this.status,
      phone: phone ?? this.phone,
      errorMessage: errorMessage ?? this.errorMessage,
      driver: driver ?? this.driver,
    );
  }
}

class AuthNotifier extends StateNotifier<AuthState> {
  final AuthRepository _repository;

  AuthNotifier(this._repository) : super(AuthState());

  Future<void> login(String phone) async {
    state = state.copyWith(status: AuthStatus.loading, errorMessage: null);
    try {
      await _repository.login(phone);
      state = state.copyWith(status: AuthStatus.otpSent, phone: phone);
    } catch (e) {
      state = state.copyWith(status: AuthStatus.error, errorMessage: e.toString());
    }
  }

  Future<void> verifyOtp(String code) async {
    if (state.phone == null) return;
    state = state.copyWith(status: AuthStatus.loading, errorMessage: null);
    try {
      final data = await _repository.verifyOtp(state.phone!, code);
      state = state.copyWith(status: AuthStatus.authenticated, driver: data['user']);
    } catch (e) {
      state = state.copyWith(status: AuthStatus.error, errorMessage: e.toString());
    }
  }

  Future<void> signInWithGoogle() async {
    state = state.copyWith(status: AuthStatus.loading, errorMessage: null);
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
    state = state.copyWith(status: AuthStatus.loading, errorMessage: null);
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
}

final authProvider = StateNotifierProvider<AuthNotifier, AuthState>((ref) {
  final repository = ref.watch(authRepositoryProvider);
  return AuthNotifier(repository);
});
