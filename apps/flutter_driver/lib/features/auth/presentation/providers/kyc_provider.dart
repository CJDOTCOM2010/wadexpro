import 'package:flutter_riverpod/flutter_riverpod.dart';
import 'package:image_picker/image_picker.dart';

class KYCState {
  final String status;
  final bool isLoading;
  final String? errorMessage;
  final String? rejectionReason;
  final bool isSubmitted;

  KYCState({
    this.status = 'pending_documents',
    this.isLoading = false,
    this.errorMessage,
    this.rejectionReason,
    this.isSubmitted = false,
  });

  bool get isVerified => status == 'active' || status == 'approved';
  bool get isPendingReview => status == 'pending_verification' || isSubmitted;

  KYCState copyWith({
    String? status,
    bool? isLoading,
    String? errorMessage,
    String? rejectionReason,
    bool? isSubmitted,
  }) {
    return KYCState(
      status: status ?? this.status,
      isLoading: isLoading ?? this.isLoading,
      errorMessage: errorMessage,
      rejectionReason: rejectionReason ?? this.rejectionReason,
      isSubmitted: isSubmitted ?? this.isSubmitted,
    );
  }
}

final kycProvider = StateNotifierProvider<KYCNotifier, KYCState>((ref) {
  return KYCNotifier();
});

class KYCNotifier extends StateNotifier<KYCState> {
  KYCNotifier() : super(KYCState());

  Future<void> checkStatus() async {
    // For now, just maintain current state — the backend KYC endpoint 
    // will be wired when the admin dashboard is ready
    state = state.copyWith(isLoading: false);
  }

  Future<bool> submitApplication({
    required String name,
    required String email,
    required String city,
    required String dob,
    required String nationalId,
    required String address,
    required String emergencyContact,
    required String vehicleBrand,
    required String vehicleModel,
    required String vehicleYear,
    required String vehiclePlate,
    required String vehicleColor,
    XFile? profilePhoto,
    XFile? licenseFront,
    XFile? vehicleReg,
    XFile? insurance,
    XFile? roadworthy,
    XFile? carPhotoFront,
    XFile? carPhotoInterior,
  }) async {
    state = state.copyWith(isLoading: true, errorMessage: null);
    try {
      // Simulate a short processing delay
      await Future.delayed(const Duration(seconds: 2));
      
      // Mark as submitted and pending review
      state = state.copyWith(
        isLoading: false,
        isSubmitted: true,
        status: 'pending_verification',
      );
      return true;
    } catch (e) {
      state = state.copyWith(isLoading: false, errorMessage: 'Submission failed: $e');
      return false;
    }
  }
}
