import 'package:flutter_riverpod/flutter_riverpod.dart';
import 'package:flutter_secure_storage/flutter_secure_storage.dart';
import 'package:google_sign_in/google_sign_in.dart';
import 'package:flutter_facebook_auth/flutter_facebook_auth.dart';
import '../../../../core/config/app_config.dart';
import '../../../../core/models/user_model.dart';
import '../../data/auth_repository.dart';
import '../../../../core/network/api_provider.dart';
import 'auth_state.dart';

final authProvider = StateNotifierProvider<AuthNotifier, AuthState>((ref) {
  final repository = ref.watch(authRepositoryProvider);
  return AuthNotifier(repository);
});

class AuthNotifier extends StateNotifier<AuthState> {
  final AuthRepository _repository;
  final _storage = const FlutterSecureStorage();

  AuthNotifier(this._repository) : super(AuthState()) {
    _checkInitialAuth();
  }

  Future<void> _checkInitialAuth() async {
    String? token;
    try {
      token = await _storage.read(key: 'access_token').timeout(const Duration(seconds: 2));
    } catch (e) {
      print('SecureStorage read bypassed/timeout: $e');
    }

    if (token != null) {
      state = state.copyWith(status: AuthStatus.authenticated);
    } else {
      state = state.copyWith(status: AuthStatus.unauthenticated);
    }
  }

  Future<void> requestOtp(String identifier) async {
    state = state.copyWith(status: AuthStatus.loading, errorMessage: null);
    try {
      await _repository.login(identifier);
      state = state.copyWith(
        status: AuthStatus.codeSent,
        identifier: identifier,
      );
    } catch (e) {
      state = state.copyWith(
        status: AuthStatus.error,
        errorMessage: e.toString(),
      );
    }
  }

  Future<void> verifyOtp(String code) async {
    if (state.identifier == null) return;
    
    state = state.copyWith(status: AuthStatus.loading, errorMessage: null);
    try {
      final responseBody = await _repository.verifyOtp(state.identifier!, code);

      // Backend returns: { status: 'success', data: { user: {...}, tokens: {...} } }
      final result = responseBody['data'] as Map<String, dynamic>? ?? responseBody;
      final tokens = result['tokens'] as Map<String, dynamic>?;
      final userJson = result['user'] as Map<String, dynamic>?;

      if (tokens == null) {
        throw Exception('Authentication server did not return tokens.');
      }

      try {
        await Future.wait([
          _storage.write(key: 'access_token', value: tokens['access_token']?.toString()),
          _storage.write(key: 'refresh_token', value: tokens['refresh_token']?.toString()),
        ]).timeout(const Duration(seconds: 2));
      } catch (e) {
        print('SecureStorage write bypassed/timeout: $e');
      }
      
      state = state.copyWith(
        status: AuthStatus.authenticated,
        user: userJson != null ? UserModel.fromJson(userJson) : null,
      );
    } catch (e) {
      state = state.copyWith(
        status: AuthStatus.error,
        errorMessage: e.toString(),
      );
    }
  }

  Future<void> logout() async {
    try {
      await _storage.deleteAll().timeout(const Duration(seconds: 2));
    } catch (e) {
      print('SecureStorage deleteAll bypassed/timeout: $e');
    }
    state = AuthState(status: AuthStatus.unauthenticated);
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
      
      // Re-use logic for token storage and state update
      final result = responseBody['data'] as Map<String, dynamic>? ?? responseBody;
      final tokens = result['tokens'] as Map<String, dynamic>?;
      final userJson = result['user'] as Map<String, dynamic>?;

      if (tokens == null) {
        throw Exception('WADEXPRO: Authentication succeeded but session sync failed.');
      }

      try {
        await Future.wait([
          _storage.write(key: 'access_token', value: tokens['access_token']?.toString()),
          _storage.write(key: 'refresh_token', value: tokens['refresh_token']?.toString()),
        ]).timeout(const Duration(seconds: 2));
      } catch (e) {
        print('SecureStorage write failure in Google flow: $e');
      }

      state = state.copyWith(
        status: AuthStatus.authenticated,
        user: userJson != null ? UserModel.fromJson(userJson) : null,
      );
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
        final userJson = resultData['user'] as Map<String, dynamic>?;

        if (tokens == null) throw Exception('WADEXPRO: Authentication succeeded but session sync failed.');

        await _storage.write(key: 'access_token', value: tokens['access_token']?.toString());
        await _storage.write(key: 'refresh_token', value: tokens['refresh_token']?.toString());

        state = state.copyWith(
          status: AuthStatus.authenticated,
          user: userJson != null ? UserModel.fromJson(userJson) : null,
        );
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
