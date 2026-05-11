import 'dart:convert';
import 'package:flutter_riverpod/flutter_riverpod.dart';
import 'package:flutter_secure_storage/flutter_secure_storage.dart';
import 'package:flutter/foundation.dart' show kIsWeb;
import '../../../../core/utils/local_storage.dart';
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
      if (kIsWeb) {
        // WADEX-Guard: High-speed localStorage check for Web
        token = getLocalStorage('wadex_access_token');
      } else {
        // Standard SecureStorage for Mobile
        token = await _storage.read(key: 'access_token');
      }
    } catch (e) {
      print('WADEXPRO: Session retrieval warning: $e');
    }

    if (token != null && token.isNotEmpty) {
      print('WADEXPRO: Valid session detected ($token). Synchronizing state.');
      
      // Load cached user data
      String? userJson;
      if (kIsWeb) {
        userJson = getLocalStorage('wadex_user_data');
      } else {
        userJson = await _storage.read(key: 'user_data');
      }

      UserModel? user;
      if (userJson != null) {
        try {
          user = UserModel.fromJson(jsonDecode(userJson));
        } catch (e) {
          print('WADEXPRO: User data corruption: $e');
        }
      }

      state = state.copyWith(status: AuthStatus.authenticated, user: user);
    } else {
      print('WADEXPRO: No active session found.');
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
        if (kIsWeb) {
          setLocalStorage('wadex_access_token', tokens['access_token']?.toString() ?? '');
          setLocalStorage('wadex_refresh_token', tokens['refresh_token']?.toString() ?? '');
        } else {
          await Future.wait([
            _storage.write(key: 'access_token', value: tokens['access_token']?.toString()),
            _storage.write(key: 'refresh_token', value: tokens['refresh_token']?.toString()),
          ]).timeout(const Duration(seconds: 2));
        }
      } catch (e) {
        print('WADEXPRO: Session write bypassed: $e');
      }
      
      final user = userJson != null ? UserModel.fromJson(userJson) : null;
      if (user != null) {
        final encodedUser = jsonEncode(user.toJson());
        if (kIsWeb) {
          setLocalStorage('wadex_user_data', encodedUser);
        } else {
          await _storage.write(key: 'user_data', value: encodedUser);
        }
      }

      state = state.copyWith(
        status: AuthStatus.authenticated,
        user: user,
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
      if (kIsWeb) {
        removeLocalStorage('wadex_access_token');
        removeLocalStorage('wadex_refresh_token');
        removeLocalStorage('wadex_user_data');
      } else {
        await _storage.deleteAll().timeout(const Duration(seconds: 2));
      }
    } catch (e) {
      print('WADEXPRO: Logout cleanup bypassed: $e');
    }
    state = AuthState(status: AuthStatus.unauthenticated);
  }

  Future<void> deleteAccount() async {
    // In a real app, this would call _repository.deleteAccount() first
    await logout();
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
        if (kIsWeb) {
          setLocalStorage('wadex_access_token', tokens['access_token']?.toString() ?? '');
          setLocalStorage('wadex_refresh_token', tokens['refresh_token']?.toString() ?? '');
        } else {
          await Future.wait([
            _storage.write(key: 'access_token', value: tokens['access_token']?.toString()),
            _storage.write(key: 'refresh_token', value: tokens['refresh_token']?.toString()),
          ]).timeout(const Duration(seconds: 2));
        }
      } catch (e) {
        print('WADEXPRO: Social session write failure: $e');
      }

      final user = userJson != null ? UserModel.fromJson(userJson) : null;
      if (user != null) {
        final encodedUser = jsonEncode(user.toJson());
        if (kIsWeb) {
          setLocalStorage('wadex_user_data', encodedUser);
        } else {
          await _storage.write(key: 'user_data', value: encodedUser);
        }
      }

      state = state.copyWith(
        status: AuthStatus.authenticated,
        user: user,
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

        if (kIsWeb) {
          setLocalStorage('wadex_access_token', tokens['access_token']?.toString() ?? '');
          setLocalStorage('wadex_refresh_token', tokens['refresh_token']?.toString() ?? '');
        } else {
          await _storage.write(key: 'access_token', value: tokens['access_token']?.toString());
          await _storage.write(key: 'refresh_token', value: tokens['refresh_token']?.toString());
        }

        final user = userJson != null ? UserModel.fromJson(userJson) : null;
        if (user != null) {
          final encodedUser = jsonEncode(user.toJson());
          if (kIsWeb) {
            setLocalStorage('wadex_user_data', encodedUser);
          } else {
            await _storage.write(key: 'user_data', value: encodedUser);
          }
        }

        state = state.copyWith(
          status: AuthStatus.authenticated,
          user: user,
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

  Future<bool> updateProfile({String? name, String? email, String? gender}) async {
    if (state.user == null) return false;
    
    try {
      final Map<String, dynamic> data = {};
      if (name != null) data['name'] = name;
      if (email != null) data['email'] = email;
      if (gender != null) data['gender'] = gender;

      final response = await _repository.updateProfile(data);
      final updatedUserJson = response['data']?['user'] ?? response['user'];
      
      if (updatedUserJson != null) {
        state = state.copyWith(user: UserModel.fromJson(updatedUserJson));
      } else {
        state = state.copyWith(
          user: state.user!.copyWith(
            name: name,
            email: email,
          ),
        );
      }

      // Persist update
      final encodedUser = jsonEncode(state.user!.toJson());
      if (kIsWeb) {
        setLocalStorage('wadex_user_data', encodedUser);
      } else {
        await _storage.write(key: 'user_data', value: encodedUser);
      }

      return true;
    } catch (e) {
      print('WADEXPRO: Profile update error: $e');
      return false;
    }
  }

  Future<bool> updateProfileImage(String filePath, {List<int>? bytes}) async {
    try {
      // Ensure we have a user object to work with (create placeholder if needed)
      UserModel currentUser = state.user ?? UserModel(
        id: 'local_user',
        name: 'WADEXPRO User',
        userType: 'customer',
        walletBalance: 0.0,
      );

      String finalUrl = filePath;
      
      try {
        final response = await _repository.updateProfilePhoto(filePath, bytes: bytes);
        final avatarUrl = response['data']?['avatar_url'] ?? response['avatar_url'];
        if (avatarUrl != null) finalUrl = avatarUrl;
      } catch (e) {
        print('WADEXPRO: Backend photo upload failed, using local fallback: $e');
      }

      final updatedUser = currentUser.copyWith(avatarUrl: finalUrl);
      state = state.copyWith(user: updatedUser);
      
      // Persist update locally
      final encodedUser = jsonEncode(updatedUser.toJson());
      if (kIsWeb) {
        setLocalStorage('wadex_user_data', encodedUser);
      } else {
        await _storage.write(key: 'user_data', value: encodedUser);
      }

      return true;
    } catch (e) {
      print('WADEXPRO: Fatal photo update error: $e');
      return false;
    }
  }
}
