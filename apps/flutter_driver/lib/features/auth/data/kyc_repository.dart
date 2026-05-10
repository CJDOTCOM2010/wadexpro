import 'package:dio/dio.dart';
import 'package:flutter/foundation.dart';
import 'package:cross_file/cross_file.dart';
import '../../../core/network/api_client.dart';

class KYCRepository {
  final ApiClient _apiClient;

  KYCRepository(this._apiClient);

  Future<Map<String, dynamic>> getKYCStatus() async {
    final response = await _apiClient.instance.get('/v1/logistics/driver/kyc/status');
    return response.data['data'];
  }

  Future<Map<String, dynamic>> uploadKYC({
    required String licenseNumber,
    required String licenseExpiresAt,
    required String licenseClass,
    XFile? idCardFront,
    XFile? idCardBack,
    XFile? driverPhoto,
  }) async {
    final Map<String, dynamic> formDataMap = {
      'license_number': licenseNumber,
      'license_expires_at': licenseExpiresAt,
      'license_class': licenseClass,
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

    final response = await _apiClient.instance.post('/v1/logistics/driver/kyc/upload', data: formData);
    return response.data['data'];
  }
}
