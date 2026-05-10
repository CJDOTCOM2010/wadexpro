import '../../../core/network/api_client.dart';

class WalletRepository {
  final ApiClient _apiClient;

  WalletRepository(this._apiClient);

  Future<Map<String, dynamic>> getBalance() async {
    final response = await _apiClient.instance.get('/v1/logistics/wallet/balance');
    return response.data;
  }

  Future<List<dynamic>> getTransactions() async {
    final response = await _apiClient.instance.get('/v1/logistics/wallet/transactions');
    return response.data['data'] ?? []; 
  }

  Future<Map<String, dynamic>> initializeTopUp(double amount) async {
    final response = await _apiClient.instance.post('/v1/logistics/wallet/topup', data: {
      'amount': amount,
    });
    return response.data['data'];
  }

  Future<Map<String, dynamic>> verifyTopUp(String reference) async {
    final response = await _apiClient.instance.get('/v1/logistics/wallet/verify', queryParameters: {
      'reference': reference,
    });
    return response.data['data'];
  }

  Future<Map<String, dynamic>> getHubData() async {
    final response = await _apiClient.instance.get('/v1/payments/wallet/hub');
    return response.data['data'];
  }

  Future<Map<String, dynamic>> checkPromo(String code) async {
    final response = await _apiClient.instance.post('/v1/payments/wallet/hub/promos/check', data: {
      'code': code,
    });
    return response.data['data'];
  }
}
