import 'package:flutter_riverpod/flutter_riverpod.dart';
import 'package:cross_file/cross_file.dart';
import '../../data/profile_repository.dart';
import '../../../../core/network/providers.dart';

class ProfileState {
  final Map<String, dynamic>? data;
  final bool isLoading;
  final String? error;

  ProfileState({this.data, this.isLoading = false, this.error});

  ProfileState copyWith({Map<String, dynamic>? data, bool? isLoading, String? error}) {
    return ProfileState(
      data: data ?? this.data,
      isLoading: isLoading ?? this.isLoading,
      error: error ?? this.error,
    );
  }

  String get verificationStatus => data?['verification_status'] ?? 'unverified';
  bool get isVerified => verificationStatus == 'verified';
}

final profileRepositoryProvider = Provider((ref) {
  final apiClient = ref.watch(apiClientProvider);
  return ProfileRepository(apiClient);
});

final profileProvider = StateNotifierProvider<ProfileNotifier, ProfileState>((ref) {
  final repository = ref.watch(profileRepositoryProvider);
  return ProfileNotifier(repository);
});

class ProfileNotifier extends StateNotifier<ProfileState> {
  final ProfileRepository _repository;

  ProfileNotifier(this._repository) : super(ProfileState()) {
    fetchProfile();
  }

  Future<void> fetchProfile() async {
    state = state.copyWith(isLoading: true, error: null);
    try {
      final data = await _repository.getProfile();
      state = state.copyWith(data: data, isLoading: false);
    } catch (e) {
      state = state.copyWith(isLoading: false, error: e.toString());
    }
  }

  Future<void> updateVehicleInfo({
    required String model,
    required String plate,
    required String color,
  }) async {
    state = state.copyWith(isLoading: true, error: null);
    try {
      final data = await _repository.updateProfile({
        'vehicle_model': model,
        'vehicle_plate': plate,
        'vehicle_color': color,
      });
      state = state.copyWith(data: data, isLoading: false);
    } catch (e) {
      state = state.copyWith(isLoading: false, error: e.toString());
    }
  }

  Future<void> uploadKYC(String type, XFile file) async {
    state = state.copyWith(isLoading: true, error: null);
    try {
      await _repository.uploadDocument(type, file);
      await fetchProfile(); // Refresh to get 'pending' status
    } catch (e) {
      state = state.copyWith(isLoading: false, error: e.toString());
    }
  }

  Future<void> submitKYC({
    required String licenseNumber,
    required String licenseClass,
    required String expiresAt,
    XFile? idCardFront,
    XFile? idCardBack,
    XFile? driverPhoto,
  }) async {
    state = state.copyWith(isLoading: true, error: null);
    try {
      await _repository.submitKYC(
        licenseNumber: licenseNumber,
        licenseClass: licenseClass,
        expiresAt: expiresAt,
        idCardFront: idCardFront,
        idCardBack: idCardBack,
        driverPhoto: driverPhoto,
      );
      await fetchProfile();
    } catch (e) {
      state = state.copyWith(isLoading: false, error: e.toString());
      rethrow;
    }
  }
}
