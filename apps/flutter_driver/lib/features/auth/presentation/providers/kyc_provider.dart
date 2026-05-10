import 'package:flutter_riverpod/flutter_riverpod.dart';
import 'package:cross_file/cross_file.dart';
import '../../data/kyc_repository.dart';
import '../../../../core/network/providers.dart';

class KYCState {
  final String status;
  final bool isLoading;
  final String? errorMessage;
  final String? rejectionReason;
  final Map<String, String?> documentUrls;

  KYCState({
    this.status = 'pending_documents',
    this.isLoading = false,
    this.errorMessage,
    this.rejectionReason,
    this.documentUrls = const {},
  });

  bool get isVerified => status == 'active';
  bool get isPendingReview => status == 'pending_verification';

  KYCState copyWith({
    String? status,
    bool? isLoading,
    String? errorMessage,
    String? rejectionReason,
    Map<String, String?>? documentUrls,
  }) {
    return KYCState(
      status: status ?? this.status,
      isLoading: isLoading ?? this.isLoading,
      errorMessage: errorMessage,
      rejectionReason: rejectionReason ?? this.rejectionReason,
      documentUrls: documentUrls ?? this.documentUrls,
    );
  }
}

final kycProvider = StateNotifierProvider<KYCNotifier, KYCState>((ref) {
  final repository = ref.watch(kycRepositoryProvider);
  return KYCNotifier(repository);
});

class KYCNotifier extends StateNotifier<KYCState> {
  final KYCRepository _repository;

  KYCNotifier(this._repository) : super(KYCState()) {
    checkStatus();
  }

  Future<void> checkStatus() async {
    state = state.copyWith(isLoading: true);
    try {
      final result = await _repository.getKYCStatus();
      state = state.copyWith(
        status: result['status'],
        rejectionReason: result['rejection_reason'],
        documentUrls: Map<String, String?>.from(result['documents']),
        isLoading: false,
      );
    } catch (e) {
      state = state.copyWith(isLoading: false, errorMessage: e.toString());
    }
  }

  Future<bool> uploadKYC({
    required String licenseNumber,
    required String licenseExpiresAt,
    required String licenseClass,
    XFile? idCardFront,
    XFile? idCardBack,
    XFile? driverPhoto,
  }) async {
    state = state.copyWith(isLoading: true);
    try {
      await _repository.uploadKYC(
        licenseNumber: licenseNumber,
        licenseExpiresAt: licenseExpiresAt,
        licenseClass: licenseClass,
        idCardFront: idCardFront,
        idCardBack: idCardBack,
        driverPhoto: driverPhoto,
      );
      await checkStatus();
      return true;
    } catch (e) {
      state = state.copyWith(isLoading: false, errorMessage: 'Upload failed: $e');
      return false;
    }
  }
}
