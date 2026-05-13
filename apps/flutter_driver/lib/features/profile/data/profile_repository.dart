import 'package:dio/dio.dart';
import 'package:flutter/foundation.dart';
import 'package:cross_file/cross_file.dart';
import '../../../core/network/api_client.dart';

class ProfileRepository {
  final ApiClient _apiClient;

  ProfileRepository(this._apiClient);

  Future<Map<String, dynamic>> getProfile() async {
    try {
      final response = await _apiClient.instance.get('/v1/logistics/profile');
      return response.data['data'];
    } on DioException catch (e) {
      throw e.response?.data['message'] ?? 'Failed to fetch profile';
    }
  }

  Future<Map<String, dynamic>> updateProfile(Map<String, dynamic> data) async {
    try {
      final response = await _apiClient.instance.patch('/v1/logistics/profile', data: data);
      return response.data['data'];
    } on DioException catch (e) {
      throw e.response?.data['message'] ?? 'Failed to update profile';
    }
  }

  Future<void> uploadDocument(String type, XFile file) async {
    try {
      final Map<String, dynamic> formDataMap = {
        'document_type': type,
      };

      if (kIsWeb) {
        formDataMap['file'] = MultipartFile.fromBytes(
          await file.readAsBytes(),
          filename: file.name,
        );
      } else {
        formDataMap['file'] = await MultipartFile.fromFile(file.path);
      }

      final formData = FormData.fromMap(formDataMap);

      await _apiClient.instance.post(
        '/v1/logistics/profile/documents',
        data: formData,
      );
    } on DioException catch (e) {
      throw e.response?.data['message'] ?? 'Failed to upload document';
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
    try {
      final Map<String, dynamic> formDataMap = {
        'license_number': licenseNumber,
        'license_class': licenseClass,
        'license_expires_at': expiresAt,
      };

      if (idCardFront != null) {
        formDataMap['id_card_front'] = kIsWeb 
            ? MultipartFile.fromBytes(await idCardFront.readAsBytes(), filename: idCardFront.name) 
            : await MultipartFile.fromFile(idCardFront.path);
      }

      if (idCardBack != null) {
        formDataMap['id_card_back'] = kIsWeb 
            ? MultipartFile.fromBytes(await idCardBack.readAsBytes(), filename: idCardBack.name) 
            : await MultipartFile.fromFile(idCardBack.path);
      }

      if (driverPhoto != null) {
        formDataMap['driver_photo'] = kIsWeb 
            ? MultipartFile.fromBytes(await driverPhoto.readAsBytes(), filename: driverPhoto.name) 
            : await MultipartFile.fromFile(driverPhoto.path);
      }

      final formData = FormData.fromMap(formDataMap);

      await _apiClient.instance.post(
        '/v1/logistics/driver/kyc/upload',
        data: formData,
      );
    } on DioException catch (e) {
      throw e.response?.data['message'] ?? 'Failed to submit KYC documents';
    }
  }
}
